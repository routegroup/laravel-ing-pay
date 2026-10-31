<?php

declare(strict_types=1);

/** @var CreateTransactionRequest $request */

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

return view('checkout.paywall', [
    'action' => app(Config::class)->env->paywallUrl(),
    'fields' => app(Paywall::class)->fields($request),
]);
// #endregion sample
