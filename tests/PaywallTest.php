<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

// The builder itself is the core's and tested there; what this package owns is
// handing it the application's configuration.

it('builds a url on the configured paywall host', function (): void {
    $url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make());

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?');
});

it('carries the configured merchant and service', function (): void {
    // Built with both blank on purpose: the shared state the provider wires
    // while testing would otherwise fill them, and this test would pass
    // whether or not the paywall asks for the identity at all.
    $request = CreateTransactionRequest::factory()->make(['merchantId' => null, 'serviceId' => null]);

    $fields = app(Paywall::class)->fields($request);

    expect($fields['merchantId'])->toBe('$merchant_id$')
        ->and($fields['serviceId'])->toBe('$service_id$');
});
