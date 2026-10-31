<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Response\PaymentResponse;

Http::fake([
    '*' => Http::response(PaymentResponse::factory()->make()->toArray()),
]);

app(Api::class)->createPayment(CreatePaymentRequest::factory()->make(['orderId' => 'ORDER-1042']));

Http::assertSent(fn (Request $request): bool => $request['orderId'] === 'ORDER-1042');
// #endregion sample
