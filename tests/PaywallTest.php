<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

it('builds a url on the configured paywall host', function (): void {
    $url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make());

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?');
});

it('carries the configured merchant and service', function (): void {
    // Blank, or the testing shared state would fill them anyway.
    $request = CreateTransactionRequest::factory()->make(['merchantId' => null, 'serviceId' => null]);

    $fields = app(Paywall::class)->fields($request);

    expect($fields['merchantId'])->toBe('$merchant_id$')
        ->and($fields['serviceId'])->toBe('$service_id$');
});
