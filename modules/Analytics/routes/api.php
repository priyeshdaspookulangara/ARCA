<?php

use Illuminate\Support\Facades\Route;
use Modules\Analytics\Core\Http\Controllers\DashboardController;
use Modules\Analytics\AdvancedAnalytics\Http\Controllers\CustomerIntelligenceController;

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

Route::prefix('dashboards')->group(function () {
    Route::get('pos', [DashboardController::class, 'getPOSDashboard']);
    Route::get('finance', [DashboardController::class, 'getFinanceDashboard']);
    Route::get('inventory', [DashboardController::class, 'getInventoryDashboard']);
    Route::get('crm', [DashboardController::class, 'getCRMDashboard']);
});

Route::get('customer-metrics', [CustomerIntelligenceController::class, 'getCustomerMetrics']);