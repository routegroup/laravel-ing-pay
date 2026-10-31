<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\PutTrustedIpsRequest;
use Routegroup\IngPay\Response\TrustedIpsResponse;

Http::fake([
    '*' => Http::response(TrustedIpsResponse::factory()->afterPut()->make()->toArray()),
]);

app(Api::class)->putTrustedIps(new PutTrustedIpsRequest(['trustedIps' => ['203.0.113.10']]));

Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
    && $request['trustedIps'] === ['203.0.113.10']);
// #endregion sample
