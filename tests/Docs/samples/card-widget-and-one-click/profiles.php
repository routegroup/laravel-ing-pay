<?php

declare(strict_types=1);

// #region sample
use Illuminate\Support\Facades\Auth;
use Routegroup\IngPay\Api;

$cards = app(Api::class)->getProfilesByCid((string) Auth::id())->paymentProfiles ?? [];

foreach ($cards as $card) {
    // offer "{$card->organization} {$card->maskedNumber}, valid to {$card->month}/{$card->year}"
}
// #endregion sample

return $cards;
