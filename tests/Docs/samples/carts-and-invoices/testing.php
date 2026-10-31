<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Value\Cart;

Http::fake([
    '*' => Http::response(PaymentResponse::factory()->make()->toArray()),
]);

$cart = Cart::factory()->make();

app(Api::class)->createPayment(CreatePaymentRequest::factory()->make(['cart' => $cart]));

Http::assertSent(fn (Request $request): bool => $request['cart'] === $cart->toArray());
// #endregion sample
