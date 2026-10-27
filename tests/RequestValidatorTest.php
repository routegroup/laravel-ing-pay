<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests;

use Illuminate\Http\Request;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Exceptions\SchemaValidationException;
use Routegroup\IngPay\Laravel\RequestValidator;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Notification\GenericNotification;

function notificationRequest(string $body, ?string $signature = null): Request
{
    $request = Request::create('/notifications', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
    ], $body);

    if ($signature !== null) {
        $request->headers->set(RequestValidator::SIGNATURE_HEADER, $signature);
    }

    return $request;
}

/** @return array{string, string} */
function signedBody(): array
{
    $body = json_encode([
        'transaction' => [
            'id' => 'tx-1',
            'amount' => 12900,
            'currency' => 'PLN',
            'status' => 'settled',
            'orderId' => 'ORDER-1',
            'serviceId' => '$service_id$',
            'type' => 'sale',
        ],
    ], JSON_THROW_ON_ERROR);

    $signature = implode(';', [
        'merchantid=$merchant_id$',
        'serviceid=$service_id$',
        'signature='.hash('sha256', $body.'$service_key$'),
        'alg=sha256',
    ]);

    return [$body, $signature];
}

it('accepts a notification the gateway signed', function (): void {
    [$body, $signature] = signedBody();

    app(RequestValidator::class)->verifySignature(notificationRequest($body, $signature));
})->throwsNoExceptions();

it('refuses one with no signature header at all', function (): void {
    [$body] = signedBody();

    expect(fn () => app(RequestValidator::class)->verifySignature(notificationRequest($body)))
        ->toThrow(InvalidSignatureException::class);
});

it('refuses one whose body was changed after signing', function (): void {
    [$body, $signature] = signedBody();

    expect(fn () => app(RequestValidator::class)->verifySignature(
        notificationRequest($body.' ', $signature)
    ))->toThrow(InvalidSignatureException::class);
});

it('reads the body exactly as it arrived, not as Laravel re-encodes it', function (): void {
    // A re-encoded body would differ in spacing or slash escaping and the
    // digest would stop matching, which is the whole reason for getContent().
    $body = '{"transaction":{"id":"tx-1","amount":12900,"currency":"PLN","status":"settled","orderId":"ORDER-1","serviceId":"$service_id$","type":"sale","url":"https://example.com/a/b"}}';

    $signature = implode(';', [
        'merchantid=$merchant_id$',
        'serviceid=$service_id$',
        'signature='.hash('sha256', $body.'$service_key$'),
        'alg=sha256',
    ]);

    app(RequestValidator::class)->verifySignature(notificationRequest($body, $signature));
})->throwsNoExceptions();

it('validates the decoded body against the notification schema', function (): void {
    [$body, $signature] = signedBody();

    expect(app(RequestValidator::class)->fromNotification(notificationRequest($body, $signature)))->toBeTrue();
});

it('refuses a body that is signed but malformed', function (): void {
    $body = json_encode(['transaction' => ['id' => 'tx-1']], JSON_THROW_ON_ERROR);

    expect(fn () => app(RequestValidator::class)->fromNotification(notificationRequest($body)))
        ->toThrow(SchemaValidationException::class);
});

it('accepts the headers Utils builds, so an integration can test its own endpoint', function (): void {
    $payload = GenericNotification::factory()->make();
    $body = json_encode($payload->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $headers = app(Utils::class)->mockHeaders($payload);

    app(RequestValidator::class)->verifySignature(
        notificationRequest($body, $headers[RequestValidator::SIGNATURE_HEADER])
    );
})->throwsNoExceptions();
