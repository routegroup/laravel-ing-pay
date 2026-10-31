<?php

declare(strict_types=1);

// #region sample
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Response\ApiErrorResponse;
use Routegroup\IngPay\Response\TransactionResponse;

Http::fake([
    '*' => Http::sequence()
        ->push(TransactionResponse::factory()->make()->toArray())
        ->push(ApiErrorResponse::factory()->make()->toArray(), 422),
]);

app(Api::class)->createTransaction(CreateTransactionRequest::factory()->make());

expect(fn () => app(Api::class)->createTransaction(CreateTransactionRequest::factory()->make()))
    ->toThrow(ApiErrorException::class);

Http::assertSentCount(2);
// #endregion sample
