<?php

use Illuminate\Support\Facades\Route;
use Modules\Fina\FI\GL\Http\Controllers\GLDocumentController;
use Modules\Fina\FI\GL\Http\Controllers\ChartOfAccountController;
use Modules\Fina\FI\GL\Http\Controllers\GLAccountController;
use Modules\Fina\FI\GL\Http\Controllers\FinancialReportController;

use Modules\Fina\FI\AR\Http\Controllers\ARInvoiceController;
use Modules\Fina\FI\AA\Http\Controllers\AssetController;

Route::prefix('fina')->group(function () {
    Route::prefix('gl')->group(function () {
        Route::get('documents', [GLDocumentController::class, 'index']);
        Route::post('documents', [GLDocumentController::class, 'store']);
        Route::get('documents/{id}', [GLDocumentController::class, 'show']);
        Route::post('documents/{id}/reverse', [GLDocumentController::class, 'reverse']);

        Route::apiResource('charts-of-accounts', ChartOfAccountController::class);
        Route::apiResource('gl-accounts', GLAccountController::class);

        Route::prefix('reports')->group(function () {
            Route::get('trial-balance', [FinancialReportController::class, 'trialBalance']);
            Route::get('profit-and-loss', [FinancialReportController::class, 'profitAndLoss']);
            Route::get('balance-sheet', [FinancialReportController::class, 'balanceSheet']);
        });
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
});
