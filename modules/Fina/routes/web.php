<?php

use Illuminate\Support\Facades\Route;

// Example Fina Web Route
// Route::get('/fina', [Modules\Fina\Http\Controllers\FinaDashboardController::class, 'index'])->name('fina.dashboard');

Route::get('/fina-dashboard-placeholder', function () {
    return "Fina Module Dashboard Placeholder (Web Route)";
});
