<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Notification\ReportNotification;
use Routegroup\IngPay\Payload;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Laravel tabs of the Notifications page. Each sample registers the route
 * it shows; the test then posts to it a body signed the way the gateway signs.
 *
 * @return TestResponse<Response>
 */
function postNotification(Payload $notification, ?string $tamperedBody = null): TestResponse
{
    $body = json_encode($notification->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    $headers = app(Utils::class)->mockHeaders($notification);

    return test()->call('POST', 'ing-pay/notifications', server: [
        'HTTP_X_IMOJE_SIGNATURE' => $headers['x-imoje-signature'],
    ], content: $tamperedBody ?? $body);
}

it('answers ok to a notification it verified', function (): void {
    require __DIR__.'/samples/notifications/verify.php';

    postNotification(GenericNotification::factory()->make())->assertOk()->assertExactJson(['status' => 'ok']);
});

it('refuses a notification whose body was changed after signing', function (): void {
    require __DIR__.'/samples/notifications/verify.php';
    $this->withoutExceptionHandling();

    postNotification(GenericNotification::factory()->make(), '{"transaction":{}}');
})->throws(InvalidSignatureException::class);

it('reads a sale and a refund notification', function (string $class): void {
    require __DIR__.'/samples/notifications/handle.php';
    $this->withoutExceptionHandling();

    postNotification($class::factory()->make())->assertOk();
})->with([GenericNotification::class, RefundNotification::class]);

it('logs a report notification without verifying it', function (): void {
    require __DIR__.'/samples/notifications/report.php';
    $log = Log::spy();

    $this->postJson('ing-pay/reports', ReportNotification::factory()->make(['fileExt' => 'csv'])->toArray())->assertOk();

    $log->shouldHaveReceived('info')->once()->withArgs(fn (string $message, array $context): bool => $context['format'] === 'csv');
});

it('tests the endpoint with headers the gateway would send', function (): void {
    require __DIR__.'/samples/notifications/handle.php';

    require __DIR__.'/samples/notifications/testing.php';
});
