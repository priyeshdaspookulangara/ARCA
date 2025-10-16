<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('mm')->group(function () {
    // Material Master
    Route::apiResource('items', \Modules\MM\Http\Controllers\MaterialController::class);

    // Inventory Management
    Route::get('stock', [\Modules\MM\Http\Controllers\StockController::class, 'index']);
    Route::post('goods-receipt', [\Modules\MM\Http\Controllers\GoodsReceiptController::class, 'store']);
    Route::post('goods-issue', [\Modules\MM\Http\Controllers\GoodsIssueController::class, 'store']);
    Route::post('transfer', [\Modules\MM\Http\Controllers\StockTransferController::class, 'store']);

    // Valuation
    Route::get('valuation', [\Modules\MM\Http\Controllers\ValuationController::class, 'index']);
});
