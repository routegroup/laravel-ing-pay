<?php

declare(strict_types=1);

// #region sample
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Routegroup\IngPay\Notification\ReportNotification;

Route::post('ing-pay/reports', function (Request $request): JsonResponse {
    $report = new ReportNotification($request->json()->all());

    Log::info('ING Pay report ready', ['url' => $report->url, 'format' => $report->fileExt]);

    return response()->json(['status' => 'ok']);
});
// #endregion sample
