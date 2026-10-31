<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Routegroup\IngPay\Laravel\RequestValidator;

Route::post('ing-pay/notifications', function (Request $request, RequestValidator $validator): JsonResponse {
    $validator->verifySignature($request);

    return response()->json(['status' => 'ok']);
});
// #endregion sample
