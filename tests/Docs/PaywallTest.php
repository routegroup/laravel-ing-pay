<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs\Paywall;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as RenderedView;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

/**
 * The Laravel tabs of the Paywall page, run in the order the page shows them.
 * Each page's test has its own namespace, so its helpers cannot meet another page's.
 */
beforeEach(function (): void {
    Route::get('orders/{order}/paid', fn (): string => 'paid')->name('orders.paid');
    Route::get('orders/{order}/failed', fn (): string => 'failed')->name('orders.failed');
    View::addLocation(__DIR__.'/views');
});

/** @return array<string, string> */
function queryOf(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

    /** @var array<string, string> $fields */
    return $fields;
}

it('redirects the payer to a signed paywall url', function (): void {
    $response = require __DIR__.'/samples/paywall/redirect.php';

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toStartWith('https://paywall.sandbox.pay.ing.pl/payment?')
        ->and(queryOf($response->getTargetUrl()))->toMatchArray([
            'merchantId' => '$merchant_id$',
            'orderId' => 'ORDER-1042',
            'urlSuccess' => 'http://localhost/orders/1042/paid',
        ])->toHaveKey('signature');

    $response = require __DIR__.'/samples/paywall/language.php';

    expect($response->getTargetUrl())->toStartWith('https://paywall.sandbox.pay.ing.pl/en/payment?');
});

it('renders the signed fields as a form', function (): void {
    $request = new CreateTransactionRequest(['amount' => 4999, 'orderId' => 'ORDER-1042']);

    $view = require __DIR__.'/samples/paywall/form.php';

    expect($view)->toBeInstanceOf(RenderedView::class)
        ->and($view->render())
        ->toContain('action="https://paywall.sandbox.pay.ing.pl/payment"')
        ->toContain('name="orderId" value="ORDER-1042"')
        ->toContain('name="signature" value="'.app(Paywall::class)->fields($request)['signature'].'"');
});

it('tests the url without a gateway', function (): void {
    require __DIR__.'/samples/paywall/testing.php';
});
