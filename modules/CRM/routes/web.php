<?php

use Illuminate\Support\Facades\Route;

// Example CRM Web Route
// Route::get('/crm', [Modules\CRM\Http\DashboardController::class, 'index'])->name('crm.dashboard');

Route::get('/crm-dashboard-placeholder', function () {
    return "CRM Module Dashboard Placeholder (Web Route)";
});
