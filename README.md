# ING Pay for Laravel

The Laravel side of the ING Pay gateway client: it binds the configuration your
application already holds, sends through Laravel's own HTTP client, and leaves
the gateway's model to [`routegroup/php-ing-pay`][core].

> **Status: under construction.** This package is being split out of
> [`routegroup/laravel-imoje`][old] and has not been released. The API, the
> paywall and notification verification are here; nothing is tagged yet.

## Requirements

PHP 8.4 and Laravel 13. Laravel 11 and 12 are not supported: this package is
tested with Pest 5, which needs `symfony/process ^8`, and Laravel allows that
only from 13. Laravel 11 is past its security window in any case.

## Configuration

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

An empty `ING_PAY_ENV` resolves to production rather than failing, because a
bare `.env` produces an empty string rather than nothing at all. An environment
that is neither `production` nor `sandbox` is refused by name.

`strict_errors`, `timeout` and `connect_timeout` are optional and default to
raising on every refusal, 30 seconds and 10 seconds.

## Sending

Resolve the client and call it:

```php
$response = app(Api::class)->createTransaction(
    CreateTransactionRequest::factory()->make()   // or build one yourself
);
```

Your merchant and service are filled in on the way out, so a request never has
to carry them.

## The paywall

The paywall is a signed form the payer's browser submits, so this builds a url
rather than sending anything:

```php
return app(Paywall::class)->createTransaction($request, Lang::PL);
```

`url()` gives the same address without the redirect, for a form, a QR code or an
e-mail. The signature is computed over the fields as they will be submitted, and
an invoice or cart is encoded at that point rather than being carried encoded.

## Notifications

```php
public function __invoke(Request $request, RequestValidator $validator): JsonResponse
{
    $validator->verifySignature($request);   // InvalidSignatureException
    $validator->fromNotification($request);  // SchemaValidationException

    $notification = new GenericNotification($request->json()->all());

    return response()->json(['status' => 'ok']);
}
```

The signature is checked against the body exactly as it arrived, not as Laravel
would re-encode it. Report notifications arrive unsigned to their own address
and must not be routed here — `verifySignature()` rejects them by design, and
accepting one would mean trusting a download url an attacker supplied.

`Utils::mockHeaders()` builds a header that passes this check, so you can test
your own endpoint without the gateway.

## Testing

Requests go through Laravel's HTTP client, so `Http::fake()` intercepts them —
which is the reason the core takes a client rather than making one:

```php
Http::fake(['*' => Http::response(['status' => 'settled'])]);

$response = app(Api::class)->getTransaction('tx-1');

Http::assertSent(fn (Request $request) => $request->url() === '...');
```

While running tests, every factory in the core also carries your configured
merchant and service, so a payload you build matches the application under test
without saying so each time.

## Licence

MIT. See [LICENSE.md](LICENSE.md).

[core]: https://github.com/routegroup/php-ing-pay
[old]: https://github.com/routegroup/laravel-imoje
