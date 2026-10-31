<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;

/**
 * The Laravel tabs of the Configuration page. The services file reads its
 * values from the environment, so the test puts them there and takes them
 * away again.
 */
const CONFIGURATION_ENV = [
    'ING_PAY_MERCHANT_ID' => 'merchant-1',
    'ING_PAY_SERVICE_ID' => 'service-1',
    'ING_PAY_SERVICE_KEY' => 'key',
    'ING_PAY_API_KEY' => 'token-abc',
    'ING_PAY_ENV' => 'sandbox',
];

afterEach(function (): void {
    foreach (array_keys(CONFIGURATION_ENV) as $name) {
        unset($_ENV[$name], $_SERVER[$name]);
    }
});

it('builds the config from the services file', function (): void {
    foreach (CONFIGURATION_ENV as $name => $value) {
        $_ENV[$name] = $_SERVER[$name] = $value;
    }

    config(['services' => require __DIR__.'/samples/configuration/services.php']);

    expect(app(Config::class))
        ->merchantId->toBe('merchant-1')
        ->serviceId->toBe('service-1')
        ->serviceKey->toBe('key')
        ->apiKey->toBe('token-abc')
        ->env->toBe(Environment::SANDBOX);
});

it('falls back to production when the environment is not set', function (): void {
    config(['services' => require __DIR__.'/samples/configuration/services.php']);

    expect(app(Config::class)->env)->toBe(Environment::PRODUCTION);
});

it('overrides a value in a test', function (): void {
    require __DIR__.'/samples/configuration/testing.php';
});
