<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Lib\Config;

config(['services.ing_pay.service_id' => 'service-test']);

expect(app(Config::class)->serviceId)->toBe('service-test');
// #endregion sample
