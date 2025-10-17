<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\SaleController;
use Modules\POS\Http\Controllers\PaymentController;
use Modules\POS\Http\Controllers\POSDashboardController;

Route::prefix('pos')->group(function () {
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::get('dashboard', [POSDashboardController::class, 'index']);
});