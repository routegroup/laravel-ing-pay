<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Types\Environment;

return [
    'ing_pay' => [
        'merchant_id' => env('ING_PAY_MERCHANT_ID'),
        'service_id' => env('ING_PAY_SERVICE_ID'),
        'service_key' => env('ING_PAY_SERVICE_KEY'),
        'api_key' => env('ING_PAY_API_KEY'),
        'env' => env('ING_PAY_ENV', Environment::PRODUCTION->value),
    ],
];
// #endregion sample
