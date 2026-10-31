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
    'orderId' => 'ORDER-1042',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'paymentMethodCode' => PaymentMethodCode::ING,
    'successReturnUrl' => route('orders.paid', 1042),
    'failureReturnUrl' => route('orders.failed', 1042),
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
]));

$transactionId = $response->transaction->id;
// #endregion sample

return $response;
