<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Value\ServiceData;

$services = app(Api::class)->getServices()->services ?? [];

$active = array_filter($services, fn (ServiceData $service): bool => $service->isActive);
// #endregion sample

return $active;
