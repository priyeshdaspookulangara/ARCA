<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\PromotionController;
use Modules\HR\Http\Controllers\TransferController;
use Modules\HR\Http\Controllers\SalaryChangeController;
use Modules\HR\Http\Controllers\PersonalDataController;

Route::prefix('hr')->group(function () {
    Route::post('promote', [PromotionController::class, 'promote']);
    Route::post('transfer', [TransferController::class, 'transfer']);
    Route::post('change-salary', [SalaryChangeController::class, 'changeSalary']);
    Route::post('update-address', [PersonalDataController::class, 'updateAddress']);
});
