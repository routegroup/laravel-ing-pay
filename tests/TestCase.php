<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Routegroup\IngPay\Laravel\IngPayServiceProvider;
use Routegroup\IngPay\Types\Environment;

class TestCase extends Orchestra
{
    /** @return list<class-string> */
    protected function getPackageProviders($app): array
    {
        return [IngPayServiceProvider::class];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('services.ing_pay', ingPayConfig());

        parent::getEnvironmentSetUp($app);
    }
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function ingPayConfig(array $overrides = []): array
{
    return array_merge([
        'merchant_id' => '$merchant_id$',
        'service_id' => '$service_id$',
        'service_key' => '$service_key$',
        'api_key' => '$api_key$',
        'env' => Environment::SANDBOX->value,
    ], $overrides);
}
