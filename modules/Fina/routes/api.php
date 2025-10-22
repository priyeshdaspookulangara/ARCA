<?php

use Illuminate\Support\Facades\Route;
use Modules\Fina\CO\PA\Infrastructure\Http\Controllers\MarketSegmentController;
use Modules\Fina\CO\PA\Infrastructure\Http\Controllers\ProfitabilityReportController;
use Modules\Fina\FI\GL\Http\Controllers\GLDocumentController;
use Modules\Fina\FI\AR\Http\Controllers\ARInvoiceController;
use Modules\Fina\FI\BL\Http\Controllers\BankAccountController;
use Modules\Fina\FI\BL\Http\Controllers\BankMasterController;
use Modules\Fina\FI\BL\Http\Controllers\BankStatementController;
use Modules\Fina\FI\AP\Http\Controllers\APInvoiceController;
use Modules\Fina\FI\AA\Http\Controllers\AssetController;
use Modules\Fina\Http\Controllers\EventListenerController;

Route::prefix('fina')->group(function () {
    Route::apiResource('market-segments', MarketSegmentController::class);
    Route::apiResource('profitability-reports', ProfitabilityReportController::class);
    Route::apiResource('gl-documents', GLDocumentController::class);
    Route::apiResource('ar-invoices', ARInvoiceController::class);
    Route::apiResource('bank-accounts', BankAccountController::class);
    Route::apiResource('bank-masters', BankMasterController::class);
    Route::apiResource('bank-statements', BankStatementController::class);
    Route::apiResource('ap-invoices', APInvoiceController::class);
    Route::apiResource('assets', AssetController::class);
    Route::post('events', [EventListenerController::class, 'handle']);
});
