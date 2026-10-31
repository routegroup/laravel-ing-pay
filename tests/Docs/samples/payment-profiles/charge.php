<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\ChargeProfileRequest;
use Routegroup\IngPay\Types\Currency;

$profileId = '2da19cca-3a30-48fe-abb1-a5df9ea88f01';

$transaction = app(Api::class)->chargeProfile(new ChargeProfileRequest([
    'paymentProfileId' => $profileId,
    'amount' => 4999,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1044',
    'title' => 'Order 1044',
]))->transaction;
// #endregion sample

return $transaction;
