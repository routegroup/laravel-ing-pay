<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Illuminate\Support\Facades\Exceptions;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;

/** The Laravel tabs of the Error Handling page. */
it('reports a refusal with its context and carries on', function (): void {
    Exceptions::fake();
    app()->instance(Client::class, FakeClient::answering(['apiErrorResponse' => [
        'code' => '404',
        'message' => 'Transaction not found',
    ]], 404));

    $transaction = require __DIR__.'/samples/error-handling/catch.php';

    expect($transaction)->toBeNull();
    Exceptions::assertReported(fn (ApiErrorException $exception): bool => $exception->status() === 404
        && $exception->getMessage() === 'Transaction not found');
});

it('reads a refusal off the response when strict errors are off', function (): void {
    config()->set('services.ing_pay.strict_errors', false);
    app()->instance(Client::class, FakeClient::answering(['message' => 'Not found'], 404));

    [$status, $message] = require __DIR__.'/samples/error-handling/lenient.php';

    expect($status)->toBe(404)
        ->and($message)->toBe('Not found');
});

it('tests a refusal with the http fake', function (): void {
    require __DIR__.'/samples/error-handling/testing.php';
});
