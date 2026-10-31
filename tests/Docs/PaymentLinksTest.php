<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs\PaymentLinks;

use Illuminate\Support\Facades\Route;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\CancelPaymentResponse;
use Routegroup\IngPay\Response\GetPaymentResponse;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Payment;

/** The Laravel tabs of the Payment Links page, run in the order the page shows them. */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
});

it('creates, reads and cancels a payment link', function (): void {
    $client = new FakeClient()
        ->queue(PaymentResponse::factory()->make([
            'payment' => Payment::factory()->make(['id' => 'pay-1', 'url' => 'https://paywall.example/pay/pay-1']),
        ])->toArray())
        ->queue(GetPaymentResponse::factory()->make(['id' => 'pay-1', 'status' => TransactionStatus::SETTLED])->toArray())
        ->queue(CancelPaymentResponse::factory()->make(['id' => 'pay-1'])->toArray());
    app()->instance(Client::class, $client);

    $response = require __DIR__.'/samples/payment-links/create.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['amount' => 4999, 'serviceId' => '$service_id$'])
        ->toHaveKey('successReturnUrl', 'http://localhost/orders/1042/paid')
        ->and($response->payment->url)->toBe('https://paywall.example/pay/pay-1');

    $payment = require __DIR__.'/samples/payment-links/read.php';

    expect($client->lastRequest()?->url)->toEndWith('/payment/pay-1')
        ->and($payment->status)->toBe(TransactionStatus::SETTLED);

    $cancelled = require __DIR__.'/samples/payment-links/cancel.php';

    expect($client->lastRequest()?->body)->toMatchArray(['paymentId' => 'pay-1'])
        ->and($cancelled->status)->toBe(TransactionStatus::CANCELLED);
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/payment-links/testing.php';
});
