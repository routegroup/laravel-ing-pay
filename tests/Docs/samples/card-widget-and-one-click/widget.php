<?php

declare(strict_types=1);

// #region sample
use Illuminate\Support\Facades\Auth;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\Lang;

$fields = app(Paywall::class)->fields(new OneClickRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'customerId' => (string) Auth::id(),
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'customerEmail' => 'anna@example.com',
    'urlSuccess' => route('orders.paid', 1042),
    'urlFailure' => route('orders.failed', 1042),
    'urlReturn' => route('checkout'),
    'locale' => Lang::PL,
]));

return view('checkout.card', [
    'script' => app(Config::class)->env->widgetUrl(),
    'scriptId' => OneClickRequest::SCRIPT_ID,
    'fields' => $fields,
]);
// #endregion sample
