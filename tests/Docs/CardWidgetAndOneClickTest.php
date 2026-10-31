<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs\CardWidgetAndOneClick;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as RenderedView;
use Routegroup\IngPay\Http\Client;
use Routegroup\IngPay\Http\FakeClient;
use Routegroup\IngPay\Response\ChargeProfileResponse;
use Routegroup\IngPay\Response\ProfilesResponse;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Value\PaymentProfile;
use Routegroup\IngPay\Value\Transaction;

/** The Laravel tabs of the Card Widget & One-Click page, run in the order the page shows them. */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
    Route::get('checkout', fn (): string => 'checkout')->name('checkout');
    View::addLocation(__DIR__.'/views');
    Auth::setUser(new GenericUser(['id' => 17]));
});

it('renders the widget with the signed fields as data attributes', function (): void {
    $view = require __DIR__.'/samples/card-widget-and-one-click/widget.php';

    expect($view)->toBeInstanceOf(RenderedView::class)
        ->and($view->render())
        ->toContain('<div id="imoje-widget__wrapper"></div>')
        ->toContain('src="https://paywall.sandbox.pay.ing.pl/js/widget.min.js" id="imoje-widget__script"')
        ->toContain('data-merchant-id="$merchant_id$"')
        ->toContain('data-customer-id="17"')
        ->toContain('data-widget-type="oneclick"')
        ->toContain('data-url-return="http://localhost/checkout"')
        ->toContain('data-signature="'.$view->getData()['fields']['signature'].'"');
});

it('charges a card the payer saved', function (): void {
    $client = new FakeClient()
        ->queue(ProfilesResponse::factory()->make([
            'paymentProfiles' => [PaymentProfile::factory()->make(['id' => 'profile-1'])],
        ])->toArray())
        ->queue(ChargeProfileResponse::factory()->make([
            'transaction' => Transaction::factory()->make(['status' => TransactionStatus::SETTLED]),
        ])->toArray());
    app()->instance(Client::class, $client);

    $cards = require __DIR__.'/samples/card-widget-and-one-click/profiles.php';

    expect($client->lastRequest()?->url)->toEndWith('/17')
        ->and($cards)->toHaveCount(1);

    $card = $cards[0];
    $transaction = require __DIR__.'/samples/card-widget-and-one-click/charge.php';

    expect($client->lastRequest()?->body)
        ->toMatchArray(['paymentProfileId' => 'profile-1', 'amount' => 2999, 'serviceId' => '$service_id$'])
        ->and($transaction->status)->toBe(TransactionStatus::SETTLED);
});

it('tests that presentation fields stay out of the signature', function (): void {
    require __DIR__.'/samples/card-widget-and-one-click/testing.php';
});
