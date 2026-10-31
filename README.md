# ING Pay for Laravel

The Laravel layer for [`routegroup/php-ing-pay`][core], the ING Pay gateway
client: it builds the client from `config/services.php`, sends through Laravel's
HTTP client, and binds the paywall builder and notification validator in the
container.

**Documentation: https://php-ing-pay.routegroup.pl**

## Installation

PHP 8.4 and Laravel 13:

```sh
composer require routegroup/laravel-ing-pay
```

## Usage

Add the service to `config/services.php`:

```php
use Routegroup\IngPay\Types\Environment;

'ing_pay' => [
    'merchant_id' => env('ING_PAY_MERCHANT_ID'),
    'service_id' => env('ING_PAY_SERVICE_ID'),
    'service_key' => env('ING_PAY_SERVICE_KEY'),
    'api_key' => env('ING_PAY_API_KEY'),
    'env' => env('ING_PAY_ENV', Environment::PRODUCTION->value),
],
```

Then resolve the `Api` and call it:

```php
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

$response = app(Api::class)->createTransaction(new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'paymentMethodCode' => PaymentMethodCode::ING,
    'successReturnUrl' => route('orders.paid', 1042),
    'failureReturnUrl' => route('orders.failed', 1042),
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska', 'email' => 'anna@example.com'],
]));

$response->transaction->id;
```

The [documentation][docs] covers the paywall, payment links, the card widget,
notifications, refunds and testing, with a Laravel tab on every sample.

## Development

`composer test` runs the same lint, type and test gate as CI, for example
`docker compose run --rm app composer test`.

## Licence

MIT. See [LICENSE.md](LICENSE.md).

[core]: https://github.com/routegroup/php-ing-pay
[docs]: https://php-ing-pay.routegroup.pl
