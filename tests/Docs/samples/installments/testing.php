<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\PaymentMethod;

Http::fake([
    '*' => Http::response(TransactionResponse::factory()->make()->toArray()),
]);

app(Api::class)->createTransaction(CreateTransactionRequest::factory()->make([
    'paymentMethod' => PaymentMethod::IMOJE_INSTALLMENTS,
    'installment' => ['period' => 12],
]));

Http::assertSent(fn (Request $request): bool => $request['installment'] === ['period' => 12]);
// #endregion sample
