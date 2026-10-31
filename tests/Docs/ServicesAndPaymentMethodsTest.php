<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Http\Response;
use Routegroup\IngPay\Response\PaymentMethodsResponse;
use Routegroup\IngPay\Response\ServiceResponse;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;
use Routegroup\IngPay\Value\ServiceData;
use Routegroup\IngPay\Value\ServicePaymentMethod;

/**
 * The Laravel tabs of the Services & Payment Methods page. GET /services
 * answers with a bare list, which the shared factory state would turn into an
 * object, so that answer is written out here.
 */
function fakeServicesGateway(FakeClient $client): FakeClient
{
    app()->instance(Client::class, $client);

    return $client;
}

it('lists the active services', function (): void {
    $client = fakeServicesGateway(new FakeClient(new Response(200, json_encode([
        ['service' => ServiceData::factory()->make(['isActive' => true])->toArray()],
        ['service' => ServiceData::factory()->make(['isActive' => false])->toArray()],
    ], JSON_THROW_ON_ERROR))));

    $active = require __DIR__.'/samples/services-and-payment-methods/services.php';

    expect($active)->toHaveCount(1)
        ->and($client->lastRequest()?->url)->toEndWith('/merchant/$merchant_id$/services');
});

it('keeps the payment methods a service has switched on', function (): void {
    $client = fakeServicesGateway(FakeClient::answering(ServiceResponse::factory()->make([
        'service' => ServiceData::factory()->make([
            'paymentMethods' => [
                ServicePaymentMethod::factory()->make(['isActive' => true])->toArray(),
                ServicePaymentMethod::factory()->make(['isActive' => false])->toArray(),
            ],
        ])->toArray(),
    ])->toArray()));

    $enabled = require __DIR__.'/samples/services-and-payment-methods/service.php';

    expect($enabled)->toHaveCount(1)
        ->and($client->lastRequest()?->url)->toEndWith('/service/$service_id$');
});

it('groups the channels for a checkout picker', function (): void {
    $client = fakeServicesGateway(new FakeClient(PaymentMethodsResponse::factory()->make()->http()));

    $picker = require __DIR__.'/samples/services-and-payment-methods/payment-methods.php';

    expect($client->lastRequest()?->url)->toEndWith('/service/$service_id$/get-payment-methods')
        ->and($client->lastRequest()?->body)->toBe(['amount' => 4999, 'currency' => 'PLN'])
        ->and($picker['Przelew tradycyjny'][1])->toBe([
            'label' => 'Split Payment',
            'paymentMethod' => PaymentMethod::WIRE_TRANSFER,
            'paymentMethodCode' => PaymentMethodCode::WIRE_TRANSFER_SPLIT,
        ]);
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/services-and-payment-methods/testing.php';
});
