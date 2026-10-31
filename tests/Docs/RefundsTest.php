<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\CanRefundResponse;
use Routegroup\IngPay\Response\RefundResponse;
use Routegroup\IngPay\Types\TransactionType;
use Routegroup\IngPay\Value\Transaction;

/** The Laravel tabs of the Refunds page, in the order the page shows them. */
it('checks a refund can be made, then makes it', function (): void {
    $client = new FakeClient()
        ->queue(CanRefundResponse::factory()->make()->toArray())
        ->queue(RefundResponse::factory()->make([
            'transaction' => Transaction::factory()->make(['id' => 'refund-1', 'type' => TransactionType::REFUND]),
        ])->toArray());
    app()->instance(Client::class, $client);

    $check = require __DIR__.'/samples/refunds/can-refund.php';

    expect($check->refundable)->toBeTrue()
        ->and($client->lastRequest()?->url)->toEndWith('/transaction/79333e36-883a-4566-a84e-97c933b37e19/can-refund');

    $refund = require __DIR__.'/samples/refunds/refund.php';

    expect($client->lastRequest()?->body)->toMatchArray(['amount' => 2000, 'type' => 'refund', 'serviceId' => '$service_id$'])
        ->and($refund->id)->toBe('refund-1');
});

it('tests a refund with the http fake', function (): void {
    require __DIR__.'/samples/refunds/testing.php';
});
