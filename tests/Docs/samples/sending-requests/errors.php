<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Exceptions\ApiErrorException;

try {
    $transaction = app(Api::class)->getTransaction('tx-1042')->transaction;
} catch (ApiErrorException $e) {
    abort_if($e->status() === 404, 404);

    throw $e;
}
// #endregion sample

return $transaction;
