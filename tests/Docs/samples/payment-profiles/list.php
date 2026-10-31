<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Api;
use Routegroup\IngPay\Value\PaymentProfile;

$profiles = app(Api::class)->getProfilesByCid('customer-1042')->paymentProfiles ?? [];

$cards = collect($profiles)
    ->filter(fn (PaymentProfile $profile): bool => $profile->isActive === true)
    ->map(fn (PaymentProfile $profile): string => "{$profile->organization} {$profile->maskedNumber}, {$profile->month}/{$profile->year}");
// #endregion sample

return $cards;
