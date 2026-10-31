<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Value\Transaction;

expect(Transaction::factory()->make()->serviceId)->toBe(config('services.ing_pay.service_id'));
// #endregion sample
