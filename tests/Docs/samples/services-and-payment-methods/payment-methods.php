<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Api\GetPaymentMethodsRequest;
use Routegroup\IngPay\Types\Currency;

$groups = app(Api::class)->getPaymentMethods(
    new GetPaymentMethodsRequest(['amount' => 4999, 'currency' => Currency::PLN]),
    config()->string('services.ing_pay.service_id'),
)->methods ?? [];

$picker = [];

foreach ($groups as $group) {
    foreach ($group->channels as $channel) {
        $picker[$group->label][] = [
            'label' => $channel->label,
            'paymentMethod' => $channel->paymentMethod,
            'paymentMethodCode' => $channel->paymentMethodCode,
        ];
    }
}
// #endregion sample

return $picker;
