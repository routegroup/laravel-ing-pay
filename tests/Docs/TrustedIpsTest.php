<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\TrustedIpsResponse;

/** The Laravel tabs of the Trusted IPs page, run in the order the page shows them. */
it('reads the list and sends it back with one more address', function (): void {
    $client = new FakeClient()
        ->queue(TrustedIpsResponse::factory()->make(['trustedIps' => ['198.51.100.7']])->toArray())
        ->queue(TrustedIpsResponse::factory()->afterPut()->make()->toArray());
    app()->instance(Client::class, $client);

    $trusted = require __DIR__.'/samples/trusted-ips/read.php';

    expect($trusted)->toBe(['198.51.100.7']);

    $saved = require __DIR__.'/samples/trusted-ips/update.php';

    expect($saved)->toBeTrue()
        ->and($client->lastRequest()?->method)->toBe('PUT')
        ->and($client->lastRequest()?->body)->toBe(['trustedIps' => ['198.51.100.7', '203.0.113.10']]);
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/trusted-ips/testing.php';
});
