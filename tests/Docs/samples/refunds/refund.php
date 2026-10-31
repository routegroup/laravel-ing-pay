<?php

declare(strict_types=1);

/** @var string $transactionId */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\RefundRequest;

$refund = app(Api::class)->createRefund(new RefundRequest([
    'amount' => 2000,
    'title' => 'Refund for ORDER-1042',
]), $transactionId)->transaction;

$refundId = $refund->id;
// #endregion sample

return $refund;
