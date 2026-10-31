<?php

declare(strict_types=1);

/**
 * @var Cart $cart
 * @var Invoice $invoice
 */

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\Currency;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Invoice;

$url = app(Paywall::class)->url(new CreateTransactionRequest([
    'amount' => 369000,
    'currency' => Currency::PLN,
    'orderId' => 'ORDER-1047',
    'customerFirstName' => 'Anna',
    'customerLastName' => 'Kowalska',
    'cart' => $cart,
    'invoice' => $invoice,
]));
// #endregion sample

return $url;
