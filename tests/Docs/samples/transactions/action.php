<?php

declare(strict_types=1);

/** @var TransactionResponse $response */

// #region sample
use Routegroup\IngPay\Response\TransactionResponse;
use Routegroup\IngPay\Types\ActionType;

$action = $response->action;

$next = match ($action?->type) {
    ActionType::REDIRECT => redirect()->away((string) $action->url),
    ActionType::TRANSFER => view('checkout.transfer', ['account' => $action->ban, 'title' => $action->title]),
    null => view('checkout.waiting'),
};
// #endregion sample

return $next;
