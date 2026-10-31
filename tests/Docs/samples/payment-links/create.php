<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreatePaymentRequest;
use Routegroup\IngPay\Types\Currency;

$response = app(Api::class)->createPayment(new CreatePaymentRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1042',
    'title' => 'Order 1042',
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
    'successReturnUrl' => route('orders.paid', 1042),
    'failureReturnUrl' => route('orders.failed', 1042),
]));

$paymentId = $response->payment->id;
$link = $response->payment->url;
// #endregion sample

return $response;
