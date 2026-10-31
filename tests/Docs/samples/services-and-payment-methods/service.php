<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Value\ServicePaymentMethod;

$service = app(Api::class)->getService(config()->string('services.ing_pay.service_id'))->service;

$enabled = array_filter(
    $service->paymentMethods,
    fn (ServicePaymentMethod $method): bool => $method->isActive === true,
);
// #endregion sample

return $enabled;
