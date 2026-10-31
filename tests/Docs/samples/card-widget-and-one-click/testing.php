<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\OneClickRequest;
use Routegroup\IngPay\Types\Lang;

$request = OneClickRequest::factory()->make()->toArray();

$polish = app(Paywall::class)->fields(new OneClickRequest([...$request, 'locale' => Lang::PL]));
$english = app(Paywall::class)->fields(new OneClickRequest([...$request, 'locale' => Lang::EN]));

expect($polish['signature'])->toBe($english['signature']);
// #endregion sample
