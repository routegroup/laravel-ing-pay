<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Response\TransactionResponse;

Http::fake([
    '*' => Http::response(TransactionResponse::factory()->make()->toArray()),
]);

app(Api::class)->createTransaction(CreateTransactionRequest::factory()->make(['amount' => 4999]));

Http::assertSent(fn (Request $request): bool => $request['amount'] === 4999);
// #endregion sample
