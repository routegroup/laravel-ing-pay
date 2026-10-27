<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Routegroup\IngPay\Laravel\Paywall;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\WidgetType;

/** @return array<string, string> */
function widgetFields(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    /** @var array<string, string> $fields */
    return $fields;
}

function widgetRequest(): OneClickRequest
{
    return OneClickRequest::factory()->make([
        'amount' => 1000,
        'orderId' => 'WIDGET-1',
        'locale' => 'pl',
        'inline' => true,
        'elementId' => 'card-widget',
        'elementEvent' => 'click',
    ]);
}

it('defaults the widget type to one-click', function (): void {
    expect((new OneClickRequest)->widgetType)->toBe(WidgetType::ONECLICK);
});

it('leaves the presentation fields out of the signature', function (): void {
    $fields = widgetFields(app(Paywall::class)->url(widgetRequest()));

    $signed = array_diff_key($fields, array_flip(OneClickRequest::UNSIGNED), ['signature' => '']);

    expect($fields['signature'])->toBe(app(Utils::class)->createSignature($signed, HashMethod::SHA256));
});

it('would produce a different signature if they were included', function (): void {
    // The point of the test above: without the exclusion the digest changes,
    // and the gateway cannot reproduce it.
    $fields = widgetFields(app(Paywall::class)->url(widgetRequest()));

    $all = $fields;
    unset($all['signature']);

    expect($fields['signature'])->not->toBe(app(Utils::class)->createSignature($all, HashMethod::SHA256));
});

it('still carries the presentation fields in the url', function (): void {
    $fields = widgetFields(app(Paywall::class)->url(widgetRequest()));

    expect($fields)->toHaveKeys(['locale', 'elementId', 'elementEvent']);
});
