<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Response\ChargeProfileResponse;

Http::fake([
    '*' => Http::response(ChargeProfileResponse::factory()->make()->toArray()),
]);

app(Api::class)->chargeProfile(ChargeProfileRequest::factory()->make(['amount' => 4999]));

Http::assertSent(fn (Request $request): bool => $request['amount'] === 4999);
// #endregion sample
