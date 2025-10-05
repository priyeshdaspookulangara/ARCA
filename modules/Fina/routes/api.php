<?php

use Illuminate\Support\Facades\Route;
use Modules\Fina\FI\GL\Http\Controllers\GLDocumentController;
use Modules\Fina\FI\AR\Http\Controllers\ARInvoiceController;
use Modules\Fina\FI\AA\Http\Controllers\AssetController;
use Modules\Fina\FI\BL\Http\Controllers\BankMasterController;
use Modules\Fina\FI\BL\Http\Controllers\BankAccountController;
use Modules\Fina\TR\Infrastructure\Http\Controllers\CashPositionController;
use Modules\Fina\TR\Infrastructure\Http\Controllers\BankBalanceController;
use Modules\Fina\TR\Infrastructure\Http\Controllers\LiquidityForecastController;
use Modules\Fina\FI\BL\Http\Controllers\BankStatementController;

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

    Route::prefix('tr')->group(function () {
        Route::apiResource('cash-positions', CashPositionController::class);
        Route::apiResource('bank-balances', BankBalanceController::class);
        Route::apiResource('liquidity-forecasts', LiquidityForecastController::class);
    });
});
