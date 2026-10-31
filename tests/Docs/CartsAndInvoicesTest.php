<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs\CartsAndInvoices;

use Illuminate\Support\Facades\Route;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\PaymentResponse;
use Routegroup\IngPay\Response\TransactionResponse;

/** The Laravel tabs of the Carts & Invoices page, run in the order the page shows them. */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
});

it('sends a cart with a payment link and an invoice with a transaction, or both through the paywall', function (): void {
    $client = new FakeClient()
        ->queue(PaymentResponse::factory()->make()->toArray())
        ->queue(TransactionResponse::factory()->make()->toArray());
    app()->instance(Client::class, $client);

    $cart = require __DIR__.'/samples/carts-and-invoices/cart.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['preselectMethodCode' => 'lease_now', 'cart' => $cart->toArray()]);

    $invoice = require __DIR__.'/samples/carts-and-invoices/invoice.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['invoice' => $invoice->toArray(), 'successReturnUrl' => 'http://localhost/orders/1046/paid']);

    $url = require __DIR__.'/samples/carts-and-invoices/paywall.php';

    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    expect($fields)->toMatchArray(['cart' => $cart->encoded(), 'invoice' => $invoice->encoded()])
        ->toHaveKey('signature');
});

it('names the field an incomplete cart is missing', function (): void {
    $field = require __DIR__.'/samples/carts-and-invoices/invalid.php';

    expect($field)->toBe('items.amount');
});

it('tests with the http fake', function (): void {
    require __DIR__.'/samples/carts-and-invoices/testing.php';
});
