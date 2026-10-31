<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\Transaction;

Http::fake([
    '*' => Http::response(GetTransactionResponse::factory()->make([
        'transaction' => Transaction::factory()->make(['id' => 'tx-1', 'status' => TransactionStatus::SETTLED]),
    ])->toArray()),
]);

expect(app(Api::class)->getTransaction('tx-1')->transaction->status)->toBe(TransactionStatus::SETTLED);

Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
    && str_ends_with($request->url(), '/transaction/tx-1'));
// #endregion sample
