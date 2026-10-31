<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\InstallmentsCalculatorRequest;

return view('checkout.installments', [
    'script' => app(Config::class)->env->installmentsUrl(),
    'scriptId' => InstallmentsCalculatorRequest::SCRIPT_ID,
    'wrapperId' => InstallmentsCalculatorRequest::WRAPPER_ID,
    'fields' => app(Paywall::class)->fields(new InstallmentsCalculatorRequest(['amount' => 129900])),
]);
// #endregion sample
