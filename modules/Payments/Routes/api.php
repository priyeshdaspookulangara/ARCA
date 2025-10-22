<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\PaymentController;
use Modules\Payments\Http\Controllers\GatewayController;
use Modules\Payments\Http\Controllers\ReconciliationController;

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

Route::prefix('payments')->group(function () {
    Route::post('initiate', [PaymentController::class, 'initiate']);
    Route::get('status/{id}', [PaymentController::class, 'status']);
    Route::post('refund/{id}', [PaymentController::class, 'refund']);
    Route::get('settlement', [ReconciliationController::class, 'settlement']);
    Route::post('reconcile/upload', [ReconciliationController::class, 'upload']);
    Route::post('webhook/{gateway}', [GatewayController::class, 'webhook']);
});
