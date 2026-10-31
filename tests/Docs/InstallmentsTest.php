<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs\Installments;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as RenderedView;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\TransactionResponse;

/** The Laravel tabs of the Installments page, run in the order the page shows them. */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
    View::addLocation(__DIR__.'/views');
});

it('renders the calculator with signed fields', function (): void {
    $view = require __DIR__.'/samples/installments/calculator.php';

    expect($view)->toBeInstanceOf(RenderedView::class);

    $fields = $view->getData()['fields'];

    expect($fields)->toMatchArray(['merchantId' => '$merchant_id$', 'amount' => 129900, 'currency' => 'PLN'])
        ->toHaveKey('signature')
        ->and($view->render())
        ->toContain('<div id="imoje-installments__wrapper"></div>')
        ->toContain('src="https://paywall.sandbox.pay.ing.pl/js/installments.js" id="imoje-installments__script"')
        ->toContain('.imojeInstallments('.json_encode($fields).')');
});

it('creates an instalment transaction', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->make()->toArray());
    app()->instance(Client::class, $client);

    require __DIR__.'/samples/installments/create.php';

    expect($client->lastRequest()?->body)->toMatchArray([
        'paymentMethod' => 'imoje_installments',
        'paymentMethodCode' => 'inbank',
        'installment' => ['period' => 12],
        'successReturnUrl' => 'http://localhost/orders/1044/paid',
    ]);
});

it('refuses a period the channel does not offer', function (): void {
    $error = require __DIR__.'/samples/installments/period.php';

    expect($error)->toBe('installment.period: must be at most 10 for inbank_0');
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/installments/testing.php';
});
