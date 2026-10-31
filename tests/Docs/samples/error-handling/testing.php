<?php

declare(strict_types=1);

// #region sample
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Response\ApiErrorResponse;

Http::fake([
    '*' => Http::response(ApiErrorResponse::factory()->make(['message' => 'Transaction not found'])->toArray(), 404),
]);

expect(fn () => app(Api::class)->getTransaction('missing'))
    ->toThrow(ApiErrorException::class, 'Transaction not found');
// #endregion sample
