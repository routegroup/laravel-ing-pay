<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;

try {
    $transaction = app(Api::class)->getTransaction('5b8d0c4e-1f7a-4c2e-9e3b-2a6f1d9c8e70')->transaction;
} catch (ApiErrorException $exception) {
    report($exception);

    $transaction = null;
}
// #endregion sample

return $transaction;
