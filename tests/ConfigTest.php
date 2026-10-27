<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use InvalidArgumentException;
use Routegroup\IngPay\Laravel\Exceptions\InvalidEnvironmentException;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Types\Environment;
use RuntimeException;

it('builds the core config from services.ing_pay', function (): void {
    $config = app(Config::class);

    expect($config->merchantId)->toBe('$merchant_id$')
        ->and($config->serviceId)->toBe('$service_id$')
        ->and($config->serviceKey)->toBe('$service_key$')
        ->and($config->apiKey)->toBe('$api_key$')
        ->and($config->env)->toBe(Environment::SANDBOX);
});

it('falls back to production when the environment is left empty', function (mixed $value): void {
    config()->set('services.ing_pay', ingPayConfig(['env' => $value]));

    expect(app(Config::class)->env)->toBe(Environment::PRODUCTION);
})->with([
    'empty string (what a bare .env produces)' => [''],
    'null' => [null],
    'whitespace' => ['   '],
]);

it('falls back to production when the key is missing entirely', function (): void {
    $config = ingPayConfig();
    unset($config['env']);

    config()->set('services.ing_pay', $config);

    expect(app(Config::class)->env)->toBe(Environment::PRODUCTION);
});

it('still resolves an explicit environment', function (Environment $environment): void {
    config()->set('services.ing_pay', ingPayConfig(['env' => $environment->value]));

    expect(app(Config::class)->env)->toBe($environment);
})->with([[Environment::SANDBOX], [Environment::PRODUCTION]]);

it('takes the enum itself as readily as its value', function (): void {
    // Only sandbox can prove this: production is also the fallback, so that
    // case would pass whether the enum is understood or ignored.
    config()->set('services.ing_pay', ingPayConfig(['env' => Environment::SANDBOX]));

    expect(app(Config::class)->env)->toBe(Environment::SANDBOX);
});

it('rejects an invalid environment with a message naming the config key', function (): void {
    config()->set('services.ing_pay', ingPayConfig(['env' => 'staging']));

    expect(fn () => app(Config::class))->toThrow(
        InvalidEnvironmentException::class,
        'Invalid [services.ing_pay.env] value [staging]. Allowed values: production, sandbox.'
    );
});

it('carries the rejected value for logging', function (): void {
    config()->set('services.ing_pay', ingPayConfig(['env' => 'staging']));

    try {
        app(Config::class);
    } catch (InvalidEnvironmentException $exception) {
        expect($exception->given)->toBe('staging')
            ->and($exception->context())->toBe([
                'given' => 'staging',
                'allowed' => ['production', 'sandbox'],
            ]);

        return;
    }

    throw new RuntimeException('InvalidEnvironmentException was not thrown.');
});

it('is catchable as a standard invalid argument', function (): void {
    config()->set('services.ing_pay', ingPayConfig(['env' => 'staging']));

    expect(fn () => app(Config::class))->toThrow(InvalidArgumentException::class);
});

it('takes the timeouts from configuration, with Laravel-like defaults', function (): void {
    expect(app(Config::class)->timeout)->toBe(30)
        ->and(app(Config::class)->connectTimeout)->toBe(10);

    config()->set('services.ing_pay', ingPayConfig(['timeout' => 5, 'connect_timeout' => 2]));

    expect(app(Config::class)->timeout)->toBe(5)
        ->and(app(Config::class)->connectTimeout)->toBe(2);
});
