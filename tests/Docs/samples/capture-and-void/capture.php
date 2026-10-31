<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Types\TransactionStatus;

$transactionId = '79333e36-883a-4566-a84e-97c933b37e19';

$transaction = app(Api::class)->getTransaction($transactionId)->transaction;

if ($transaction->status === TransactionStatus::AUTHORIZED) {
    $transaction = app(Api::class)->captureTransaction($transactionId)->transaction;
}
// #endregion sample

return $transaction;
