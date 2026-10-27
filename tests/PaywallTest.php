<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Routegroup\IngPay\Laravel\Paywall;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Value\Cart;

/** @return array<string, string> */
function fieldsOf(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    /** @var array<string, string> $fields */
    return $fields;
}

it('builds a url on the configured paywall host', function (): void {
    $url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make());

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?');
});

it('puts the language in the path when one is asked for', function (): void {
    $url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make(), Lang::PL);

    expect($url)->toStartWith('https://paywall.sandbox.pay.ing.pl/pl/payment?');
});

it('carries the configured merchant and service', function (): void {
    // Built with both blank on purpose: the shared state the provider wires
    // while testing would otherwise fill them, and this test would pass
    // whether or not the paywall asks for the identity at all.
    $request = CreateTransactionRequest::factory()->make(['merchantId' => null, 'serviceId' => null]);

    $fields = fieldsOf(app(Paywall::class)->url($request));

    expect($fields['merchantId'])->toBe('$merchant_id$')
        ->and($fields['serviceId'])->toBe('$service_id$');
});

it('signs the url with a signature that verifies back', function (): void {
    $url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make());
    $fields = fieldsOf($url);

    $signature = $fields['signature'];
    unset($fields['signature']);

    expect($signature)->toBe(app(Utils::class)->createSignature($fields, HashMethod::SHA256));
});

it('encodes a cart rather than expanding it into the query', function (): void {
    $cart = Cart::factory()->make();

    $fields = fieldsOf(app(Paywall::class)->url(
        CreateTransactionRequest::factory()->make(['cart' => $cart])
    ));

    expect($fields['cart'])->toBe($cart->encoded())
        ->and($fields)->not->toHaveKey('cart[items][0][name]');
});

it('redirects to the url it built', function (): void {
    $request = CreateTransactionRequest::factory()->make();

    $redirect = app(Paywall::class)->createTransaction($request);

    expect($redirect->getTargetUrl())->toBe(app(Paywall::class)->url($request));
});
