<?php

declare(strict_types=1);

// #region sample
use Illuminate\Support\Facades\Log;
use Routegroup\IngPay\Api\CreateTransactionRequest;
use Routegroup\IngPay\Types\PaymentMethod;

$request = new CreateTransactionRequest([
    'amount' => 4999,
    'currency' => 'PLN',
    'orderId' => 'ORDER-1042',
    'paymentMethod' => PaymentMethod::PAY_BY_LINK,
    'customer' => ['firstName' => 'Anna', 'lastName' => 'Kowalska'],
]);

Log::info('Sending a transaction to ING Pay', $request->toArray());
// #endregion sample

return $request;
