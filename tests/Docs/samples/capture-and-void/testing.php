<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Response\TransactionResponse;

Http::fake([
    '*' => Http::response(TransactionResponse::factory()->make()->toArray()),
]);

app(Api::class)->captureTransaction('tx-1');

Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
    && str_ends_with($request->url(), '/transaction/tx-1/capture'));
// #endregion sample
