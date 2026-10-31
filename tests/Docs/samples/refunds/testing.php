<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\RefundRequest;
use Routegroup\IngPay\Response\RefundResponse;

Http::fake([
    '*' => Http::response(RefundResponse::factory()->make()->toArray()),
]);

app(Api::class)->createRefund(new RefundRequest(['amount' => 2000]), 'tx-1');

Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/transaction/tx-1/refund')
    && $request['amount'] === 2000);
// #endregion sample
