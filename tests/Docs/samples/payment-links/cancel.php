<?php

declare(strict_types=1);

/** @var string $paymentId */

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\CancelPaymentRequest;

$cancelled = app(Api::class)->cancelPayment(new CancelPaymentRequest(['paymentId' => $paymentId]));
// #endregion sample

return $cancelled;
