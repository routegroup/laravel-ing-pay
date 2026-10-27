<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Value\Transaction;

it('bakes the application configuration into every factory while testing', function (): void {
    expect(Transaction::factory()->make()->serviceId)->toBe('$service_id$')
        ->and(CreateTransactionRequest::factory()->make()->serviceId)->toBe('$service_id$');
});

it('follows the configuration when a test changes it', function (): void {
    config()->set('services.ing_pay', ingPayConfig(['service_id' => 'other-service']));

    expect(Transaction::factory()->make()->serviceId)->toBe('other-service');
});

it('still lets a test override it outright', function (): void {
    expect(Transaction::factory()->make(['serviceId' => 'explicit'])->serviceId)->toBe('explicit');
});
