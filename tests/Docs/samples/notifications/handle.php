<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Routegroup\IngPay\Laravel\RequestValidator;
use Routegroup\IngPay\Notification\GenericNotification;
use Routegroup\IngPay\Notification\RefundNotification;
use Routegroup\IngPay\Types\TransactionStatus;
use Routegroup\IngPay\Types\TransactionType;

Route::post('ing-pay/notifications', function (Request $request, RequestValidator $validator): JsonResponse {
    $validator->verifySignature($request);
    $validator->fromNotification($request);

    $notification = $request->json('transaction.type') === TransactionType::REFUND->value
        ? new RefundNotification($request->json()->all())
        : new GenericNotification($request->json()->all());

    if ($notification->transaction?->status === TransactionStatus::SETTLED) {
        // find the order by $notification->transaction->orderId and fulfil it
    }

    return response()->json(['status' => 'ok']);
});
// #endregion sample
