<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;

$response = app(Api::class)->getServices();

if ($response->failed()) {
    $status = $response->http()->status;
    $message = $response->error()?->message;
}
// #endregion sample

return [$status ?? null, $message ?? null];
