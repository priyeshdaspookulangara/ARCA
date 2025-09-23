<?php

use Illuminate\Support\Facades\Route;
use Modules\Fina\FI\GL\Http\Controllers\GLDocumentController;
use Modules\Fina\FI\AR\Http\Controllers\ARInvoiceController;
use Modules\Fina\FI\AA\Http\Controllers\AssetController;
use Modules\Fina\FI\BL\Http\Controllers\BankMasterController;
use Modules\Fina\FI\BL\Http\Controllers\BankAccountController;
use Modules\Fina\FI\BL\Http\Controllers\BankStatementController;
use Modules\Fina\PC\Http\Controllers\MaterialCostController;
use Modules\Fina\PC\Http\Controllers\InventoryValuationController;
use Modules\Fina\PC\Http\Controllers\CostObjectControllingController;
use Modules\Fina\PC\Http\Controllers\MaterialCostController;
use Modules\Fina\PC\Http\Controllers\InventoryValuationController;
use Modules\Fina\PC\Http\Controllers\CostObjectControllingController;

Route::prefix('fina')->group(function () {
    Route::prefix('gl')->group(function () {
        Route::post('documents', [GLDocumentController::class, 'store']);
        Route::get('documents/{id}', [GLDocumentController::class, 'show']);
    });

    Route::prefix('ap')->group(function () {
        Route::post('invoices', [APInvoiceController::class, 'store']);
        Route::get('invoices/{id}', [APInvoiceController::class, 'show']);
    });

    Route::prefix('ar')->group(function () {
        Route::post('invoices', [ARInvoiceController::class, 'store']);
        Route::get('invoices/{id}', [ARInvoiceController::class, 'show']);
    });

    Route::prefix('aa')->group(function () {
        Route::post('assets', [AssetController::class, 'store']);
        Route::get('assets/{id}', [AssetController::class, 'show']);
    });

    Route::prefix('bl')->group(function () {
        Route::post('banks', [BankMasterController::class, 'store']);
        Route::get('banks/{id}', [BankMasterController::class, 'show']);
        Route::put('banks/{id}', [BankMasterController::class, 'update']);
        Route::delete('banks/{id}', [BankMasterController::class, 'destroy']);

        Route::post('bank-accounts', [BankAccountController::class, 'store']);
        Route::get('bank-accounts/{id}', [BankAccountController::class, 'show']);
        Route::put('bank-accounts/{id}', [BankAccountController::class, 'update']);
        Route::delete('bank-accounts/{id}', [BankAccountController::class, 'destroy']);

        Route::post('bank-statements', [BankStatementController::class, 'store']);
        Route::get('bank-statements/{id}', [BankStatementController::class, 'show']);
        Route::put('bank-statements/{id}', [BankStatementController::class, 'update']);
        Route::delete('bank-statements/{id}', [BankStatementController::class, 'destroy']);
    });

    Route::prefix('pc')->group(function () {
        Route::post('material-costs', [MaterialCostController::class, 'store']);
        Route::get('material-costs/{id}', [MaterialCostController::class, 'show']);
        Route::put('material-costs/{id}', [MaterialCostController::class, 'update']);
        Route::delete('material-costs/{id}', [MaterialCostController::class, 'destroy']);

        Route::post('inventory-valuations', [InventoryValuationController::class, 'store']);
        Route::get('inventory-valuations/{id}', [InventoryValuationController::class, 'show']);
        Route::put('inventory-valuations/{id}', [InventoryValuationController::class, 'update']);
        Route::delete('inventory-valuations/{id}', [InventoryValuationController::class, 'destroy']);

        Route::post('cost-object-controlling', [CostObjectControllingController::class, 'store']);
        Route::get('cost-object-controlling/{id}', [CostObjectControllingController::class, 'show']);
        Route::put('cost-object-controlling/{id}', [CostObjectControllingController::class, 'update']);
        Route::delete('cost-object-controlling/{id}', [CostObjectControllingController::class, 'destroy']);
    });
});
