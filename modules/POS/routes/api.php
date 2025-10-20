<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\SaleController;
use Modules\POS\Http\Controllers\PaymentController;
use Modules\POS\Http\Controllers\POSDashboardController;
use Modules\POS\Http\Controllers\SyncController;

Route::prefix('pos')->group(function () {
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::get('dashboard', [POSDashboardController::class, 'index']);
});

Route::prefix('pos-sync/v1')->group(function () {
    Route::post('events', [SyncController::class, 'ingest']);
    Route::post('sync/offline-batch', [SyncController::class, 'syncOfflineBatch']);
});