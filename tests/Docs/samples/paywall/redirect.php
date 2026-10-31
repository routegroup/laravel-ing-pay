<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;

$request = new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'customerEmail' => 'anna@example.com',
    'urlSuccess' => route('orders.paid', 1042),
    'urlFailure' => route('orders.failed', 1042),
]);

return redirect(app(Paywall::class)->url($request));
// #endregion sample
