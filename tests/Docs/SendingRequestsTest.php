<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Laravel\Http\LaravelClient;
use Routegroup\IngPay\Response\GetTransactionResponse;
use Routegroup\IngPay\Value\Transaction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** The Laravel tabs of the Sending Requests page. */
it('resolves an api that sends through the http client', function (): void {
    $api = require __DIR__.'/samples/sending-requests/api.php';

    expect($api)->toBeInstanceOf(Api::class)
        ->and(app(Client::class))->toBeInstanceOf(LaravelClient::class);
});

it('reads a transaction when the gateway has it', function (): void {
    app()->instance(Client::class, FakeClient::answering(GetTransactionResponse::factory()->make()->toArray()));

    $transaction = require __DIR__.'/samples/sending-requests/errors.php';

    expect($transaction)->toBeInstanceOf(Transaction::class);
});

it('turns an unknown transaction into a 404', function (): void {
    app()->instance(Client::class, FakeClient::answering(status: 404));

    expect(fn (): mixed => require __DIR__.'/samples/sending-requests/errors.php')
        ->toThrow(NotFoundHttpException::class);
});

it('rethrows any other refusal', function (): void {
    app()->instance(Client::class, FakeClient::answering(['message' => 'Internal error'], 500));

    expect(fn (): mixed => require __DIR__.'/samples/sending-requests/errors.php')
        ->toThrow(ApiErrorException::class, 'Internal error');
});

it('binds a fake client in the container', function (): void {
    require __DIR__.'/samples/sending-requests/testing.php';
});
