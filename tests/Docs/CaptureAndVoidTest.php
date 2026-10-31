<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

/** The Laravel tabs of the Capture & Void page. */
it('captures a transaction that is authorised', function (): void {
    $client = new FakeClient()
        ->queue(GetTransactionResponse::factory()->make([
            'transaction' => Transaction::factory()->make(['status' => TransactionStatus::AUTHORIZED]),
        ])->toArray())
        ->queue(TransactionResponse::factory()->make()->toArray());
    app()->instance(Client::class, $client);

    require __DIR__.'/samples/capture-and-void/capture.php';

    expect($client->sentCount())->toBe(2)
        ->and($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/capture');
});

it('voids a transaction', function (): void {
    $client = FakeClient::answering(TransactionResponse::factory()->make()->toArray());
    app()->instance(Client::class, $client);

    require __DIR__.'/samples/capture-and-void/void.php';

    expect($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/void');
});

it('tests a capture with the http fake', function (): void {
    require __DIR__.'/samples/capture-and-void/testing.php';
});
