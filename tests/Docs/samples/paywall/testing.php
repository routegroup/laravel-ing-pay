<?php

declare(strict_types=1);

// #region sample
use Routegroup\IngPay\Paywall;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;

$url = app(Paywall::class)->url(CreateTransactionRequest::factory()->make(['orderId' => 'ORDER-1042']));

parse_str((string) parse_url($url, PHP_URL_QUERY), $fields);

expect($fields)
    ->toHaveKey('orderId', 'ORDER-1042')
    ->toHaveKey('signature');
// #endregion sample
