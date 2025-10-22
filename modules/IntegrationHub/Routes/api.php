<?php

use Illuminate\Support\Facades\Route;
use Modules\IntegrationHub\Http\Controllers\IntegrationController;
use Modules\IntegrationHub\Http\Controllers\WebhookController;
use Modules\IntegrationHub\Http\Controllers\LogController;

Route::prefix('integration')->group(function() {
    Route::get('profiles', [IntegrationController::class, 'profiles']);
    Route::post('profiles', [IntegrationController::class, 'createProfile']);
    Route::post('dispatch', [IntegrationController::class, 'dispatch']);
    Route::post('webhook/{source}', [WebhookController::class, 'handle']);
    Route::get('logs', [LogController::class, 'index']);
});
