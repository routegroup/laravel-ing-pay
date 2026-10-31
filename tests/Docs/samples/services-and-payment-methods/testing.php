<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Response\ServiceResponse;

Http::fake([
    '*' => Http::response(ServiceResponse::factory()->make()->toArray()),
]);

$serviceId = config()->string('services.ing_pay.service_id');

expect(app(Api::class)->getService($serviceId)->service->paymentMethods)->not->toBeEmpty();

Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), "/service/{$serviceId}"));
// #endregion sample
