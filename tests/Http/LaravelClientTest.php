<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Http;

use Illuminate\Http\Client\Request as LaravelRequest;
use Illuminate\Support\Facades\Http;
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\Request;
use Routegroup\IngPay\Laravel\Http\LaravelClient;
use Routegroup\IngPay\Response\TransactionResponse;

it('binds our client interface to the laravel one', function (): void {
    expect(app(Client::class))->toBeInstanceOf(LaravelClient::class);
});

it('is intercepted by Http::fake, which is why the seam exists', function (): void {
    Http::fake(['*' => Http::response(['status' => 'ok'], 200)]);

    $answer = app(Client::class)->send(Request::get('https://api.sandbox.pay.ing.pl/v1/services'));

    expect($answer->status)->toBe(200)
        ->and($answer->json())->toBe(['status' => 'ok']);

    Http::assertSent(fn (LaravelRequest $request): bool => $request->url() === 'https://api.sandbox.pay.ing.pl/v1/services');
});

it('sends a body as json and carries our headers', function (): void {
    Http::fake(['*' => Http::response([], 201)]);

    app(Client::class)->send(
        Request::post('https://api.sandbox.pay.ing.pl/v1/transaction', ['amount' => 1000])
            ->withHeaders(['Authorization' => 'Bearer token-abc'])
    );

    Http::assertSent(fn (LaravelRequest $request): bool => $request->method() === 'POST'
        && $request->hasHeader('Authorization', 'Bearer token-abc')
        && $request->data() === ['amount' => 1000]);
});

it('brings a refusal back rather than raising inside laravel', function (): void {
    Http::fake(['*' => Http::response(['apiErrorResponse' => ['message' => 'nope']], 422)]);

    $answer = app(Client::class)->send(Request::get('https://api.sandbox.pay.ing.pl/v1/services'));

    expect($answer->failed())->toBeTrue()
        ->and($answer->status)->toBe(422);
});

it('drives the whole api through the faked client', function (): void {
    Http::fake(['*' => Http::response(TransactionResponse::factory()->make()->toArray(), 200)]);

    $response = app(Api::class)->createTransaction(
        CreateTransactionRequest::factory()->make(['serviceId' => null])
    );

    expect($response->successful())->toBeTrue()
        ->and($response->http()->status)->toBe(200);

    Http::assertSent(fn (LaravelRequest $request): bool => $request->data()['serviceId'] === '$service_id$');
});
