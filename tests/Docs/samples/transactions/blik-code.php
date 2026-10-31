<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

$response = app(Api::class)->createTransaction(new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1043',
    'paymentMethod' => PaymentMethod::BLIK,
    'paymentMethodCode' => PaymentMethodCode::BLIK,
    'blikCode' => '777123',
    'successReturnUrl' => route('orders.paid', 1043),
    'failureReturnUrl' => route('orders.failed', 1043),
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
]));
// #endregion sample

return $response;
