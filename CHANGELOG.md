# Changelog

All notable changes to `routegroup/laravel-ing-pay` will be documented in this file.

Nothing is released yet. This package is being split out of
[`routegroup/laravel-imoje`](https://github.com/routegroup/laravel-imoje); until
the first tag, the history of the code that lands here lives in that repository.

## v1.0.0 - 2026-10-31

First release of the Laravel integration for ING Pay, split out of [`routegroup/laravel-imoje`](https://github.com/routegroup/laravel-imoje).

- Wraps [`routegroup/php-ing-pay`](https://github.com/routegroup/php-ing-pay) v1.0.0: the service provider binds the API client, the paywall and notification verification, and sends requests through Laravel's HTTP client, so `Http::fake()` works in your own tests.
- Configuration lives under `services.ing_pay.*`, with the environment picked by `ING_PAY_ENV`.
- Requires PHP 8.4 and Laravel 13.
- Namespace `Routegroup\IngPay\Laravel\`.

Coming from `routegroup/laravel-imoje`: both the package name and the namespace changed, so the upgrade is a deliberate rewrite of your `require`, imports and config keys rather than a version bump.

Documentation: https://php-ing-pay.routegroup.pl
