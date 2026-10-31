<?php

declare(strict_types=1);

/** @var CreateTransactionRequest $request */

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Lang;

return redirect(app(Paywall::class)->url($request, Lang::EN));
// #endregion sample
