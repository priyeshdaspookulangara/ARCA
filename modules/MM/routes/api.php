<?php

use Illuminate\Support\Facades\Route;
use Modules\MM\MaterialMaster\Http\Controllers\MaterialController;
use Modules\MM\InventoryManagement\Http\Controllers\StockController;
use Modules\MM\InventoryManagement\Http\Controllers\GoodsReceiptController;
use Modules\MM\InventoryManagement\Http\Controllers\GoodsIssueController;
use Modules\MM\InventoryManagement\Http\Controllers\StockTransferController;
use Modules\MM\Valuation\Http\Controllers\ValuationController;

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

Route::prefix('mm')->group(function () {
    // Material Master
    Route::apiResource('items', MaterialController::class);

    // Inventory Management
    Route::get('stock', [StockController::class, 'index']);
    Route::post('goods-receipt', [GoodsReceiptController::class, 'store']);
    Route::post('goods-issue', [GoodsIssueController::class, 'store']);
    Route::post('transfer', [StockTransferController::class, 'store']);

    // Valuation
    Route::get('valuation', [ValuationController::class, 'index']);
});