<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Types\PaymentMethod;
use Routegroup\IngPay\Types\PaymentMethodCode;

$response = app(Api::class)->createTransaction(new CreateTransactionRequest([
    'amount' => 129900,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1044',
    'paymentMethod' => PaymentMethod::IMOJE_INSTALLMENTS,
    'paymentMethodCode' => PaymentMethodCode::INBANK,
    'installment' => ['period' => 12],
    'successReturnUrl' => route('orders.paid', 1044),
    'failureReturnUrl' => route('orders.failed', 1044),
    'customer' => [
        'firstName' => 'Anna',
        'lastName' => 'Kowalska',
        'email' => 'anna@example.com',
    ],
]));
// #endregion sample

return $response;
