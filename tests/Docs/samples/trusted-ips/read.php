<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;

$trusted = app(Api::class)->getTrustedIps()->trustedIps ?? [];
// #endregion sample

return $trusted;
