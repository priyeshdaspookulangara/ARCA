<?php

use Illuminate\Support\Facades\Route;
use Modules\SD\Http\Controllers\SalesOrderController;
use Modules\SD\Http\Controllers\DeliveryController;
use Modules\SD\Http\Controllers\BillingController;
use Modules\SD\Http\Controllers\CustomerController;

Route::prefix('sd')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('sales-orders', SalesOrderController::class);
    Route::apiResource('deliveries', DeliveryController::class);
    Route::apiResource('billing', BillingController::class);
});