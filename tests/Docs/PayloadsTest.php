<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Illuminate\Support\Facades\Log;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Fee;
use Routegroup\IngPay\Value\Transaction;

/** The Laravel tabs of the Payloads page. */
it('builds a request from arrays and enums and logs it', function (): void {
    $log = Log::spy();

    /** @var CreateTransactionRequest $request */
    $request = require __DIR__.'/samples/payloads/build.php';

    expect($request->currency)->toBe(Currency::PLN);
    $log->shouldHaveReceived('info')->once()->with('Sending a transaction to ING Pay', [
        'type' => 'sale',
        'amount' => 4999,
        'currency' => 'PLN',
        'orderId' => 'ORDER-1042',
        'paymentMethod' => 'pbl',
        'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska'],
    ]);
});

it('reads typed values off a response', function (): void {
    app()->instance(Client::class, FakeClient::answering(GetTransactionResponse::factory()->make([
        'transaction' => Transaction::factory()->make([
            'status' => TransactionStatus::SETTLED,
            'fee' => new Fee(['merchant' => 120]),
        ]),
    ])->toArray()));

    [$settled, $fee, $answer] = require __DIR__.'/samples/payloads/read.php';

    expect($settled)->toBeTrue()
        ->and($fee)->toBe(120)
        ->and($answer)->toBeJson();
});

it('builds payloads with factories that carry the configured service', function (): void {
    /** @var list<Transaction> $history */
    [, $settled, $history, $ours] = require __DIR__.'/samples/payloads/factories.php';

    expect($ours)->toBeTrue()
        ->and($settled->status)->toBe(TransactionStatus::SETTLED)
        ->and(array_map(fn (Transaction $item): ?TransactionStatus => $item->status, $history))->toBe([
            TransactionStatus::PENDING,
            TransactionStatus::SETTLED,
            TransactionStatus::PENDING,
            TransactionStatus::SETTLED,
        ]);
});
