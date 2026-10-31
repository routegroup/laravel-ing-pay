<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as RenderedView;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\ActionType;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

/**
 * The Laravel tabs of the Transactions page, run in the order the page shows
 * them. The samples resolve everything from the container, so the fake goes
 * in there, and the routes and views they name are the test's to provide.
 */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
    View::addLocation(__DIR__.'/views');
});

function fakeGateway(TransactionResponse $answer): FakeClient
{
    $client = FakeClient::answering($answer->toArray());
    app()->instance(Client::class, $client);

    return $client;
}

it('creates a transaction and redirects the payer where the gateway says', function (): void {
    $client = fakeGateway(TransactionResponse::factory()->make([
        'action' => ['type' => ActionType::REDIRECT, 'url' => 'https://paywall.example/go'],
    ]));

    $response = require __DIR__.'/samples/transactions/create.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['amount' => 4999, 'serviceId' => '$service_id$'])
        ->toHaveKey('successReturnUrl', 'http://localhost/orders/1042/paid');

    $next = require __DIR__.'/samples/transactions/action.php';

    expect($next)->toBeInstanceOf(RedirectResponse::class)
        ->and($next->getTargetUrl())->toBe('https://paywall.example/go');
});

it('shows a waiting page when blik was paid with a code', function (): void {
    fakeGateway(TransactionResponse::factory()->make(['action' => null]));

    $response = require __DIR__.'/samples/transactions/blik-code.php';
    $next = require __DIR__.'/samples/transactions/action.php';

    expect($next)->toBeInstanceOf(RenderedView::class)
        ->and($next->name())->toBe('checkout.waiting');
});

it('reads a transaction back by its id', function (): void {
    $client = fakeGateway(TransactionResponse::factory()->make([
        'transaction' => Transaction::factory()->make(['id' => 'tx-1', 'status' => TransactionStatus::SETTLED]),
    ]));

    require __DIR__.'/samples/transactions/create.php';
    $transaction = require __DIR__.'/samples/transactions/read.php';

    expect($client->lastRequest()?->url)->toEndWith('/transaction/tx-1')
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED);
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/transactions/testing.php';
});
