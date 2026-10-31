<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Tests\Docs;

/** The Laravel tabs of the Testing page. Each sample is a test body in its own right. */
it('fakes the gateway and asserts what was sent', function (): void {
    require __DIR__.'/samples/testing/fake.php';
});

it('queues answers in order, a refusal among them', function (): void {
    require __DIR__.'/samples/testing/queue.php';
});

it('bakes the configuration into every factory', function (): void {
    require __DIR__.'/samples/testing/shared-state.php';
});
