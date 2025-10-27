<?php

use Illuminate\Support\Facades\Route;
use Modules\TaxEngine\Http\Controllers\TaxConfigController;
use Modules\TaxEngine\Http\Controllers\TaxComputationController;
use Modules\TaxEngine\Http\Controllers\TaxReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('tax')->group(function () {
    Route::prefix('config')->group(function () {
        Route::get('list', [TaxConfigController::class, 'index']);
        Route::post('update', [TaxConfigController::class, 'store']);
    });

    Route::post('compute', [TaxComputationController::class, 'store']);
    Route::get('transactions/{id}', [TaxComputationController::class, 'show']);

    Route::prefix('report')->group(function () {
        Route::get('summary', [TaxReportController::class, 'summary']);
    });

    Route::post('reconcile', [TaxReconciliationController::class, 'store']);
});
