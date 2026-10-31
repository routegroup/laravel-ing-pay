<?php

declare(strict_types=1);

/** @var TestCase $this */
// #region sample
use Illuminate\Foundation\Testing\TestCase;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Notification\GenericNotification;

$notification = GenericNotification::factory()->make();

$body = json_encode($notification->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

$headers = app(Utils::class)->mockHeaders($notification);

$this->call('POST', 'ing-pay/notifications', server: [
    'HTTP_X_IMOJE_SIGNATURE' => $headers['x-imoje-signature'],
], content: $body)->assertOk();
// #endregion sample
