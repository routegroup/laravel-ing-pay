<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Factories\Factory;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Laravel\Exceptions\InvalidEnvironmentException;
use Routegroup\IngPay\Laravel\Http\LaravelClient;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Url;
use Routegroup\IngPay\Types\Environment;

class IngPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Config::class, function (Application $app): Config {
            $config = $app->make('config');

            return new Config(
                (string) $config->get('services.ing_pay.merchant_id'),
                (string) $config->get('services.ing_pay.service_id'),
                (string) $config->get('services.ing_pay.service_key'),
                (string) $config->get('services.ing_pay.api_key'),
                $this->resolveEnvironment($config->get('services.ing_pay.env')),
                (bool) $config->get('services.ing_pay.strict_errors', true),
                $config->get('services.ing_pay.timeout') ?? 30,
                $config->get('services.ing_pay.connect_timeout') ?? 10,
            );
        });

        $this->app->bind(Client::class, fn (Application $app): Client => new LaravelClient($app->make(Config::class)));
        $this->app->bind(Url::class, fn (Application $app): Url => new Url($app->make(Config::class)));

        $this->app->bind(Api::class, fn (Application $app): Api => new Api(
            $app->make(Client::class),
            $app->make(Config::class),
            $app->make(Url::class),
        ));
    }

    /**
     * While testing, every payload a factory builds carries the application's
     * own merchant and service, so a test asserts against the configuration it
     * is running under rather than against generated identifiers.
     */
    public function boot(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        Factory::useSharedState(function (): array {
            $config = $this->app->make(Config::class);

            return [
                'merchantId' => $config->merchantId,
                'serviceId' => $config->serviceId,
            ];
        });
    }

    /** env('ING_PAY_ENV') yields an empty string, not null, so a default never applies. */
    private function resolveEnvironment(mixed $value): Environment
    {
        if (! is_string($value) || trim($value) === '') {
            return Environment::PRODUCTION;
        }

        return Environment::tryFrom(trim($value))
            ?? throw new InvalidEnvironmentException(trim($value));
    }

    /** @return list<class-string> */
    public function provides(): array
    {
        return [Config::class, Client::class, Url::class, Api::class];
    }
}
