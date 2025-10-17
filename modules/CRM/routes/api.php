<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\CampaignController;
use Modules\CRM\Http\Controllers\CustomerController;
use Modules\CRM\Http\Controllers\ActivityLogController;
use Modules\CRM\Http\Controllers\LeadController;
use Modules\CRM\Http\Controllers\LoyaltyController;
use Modules\CRM\Http\Controllers\InteractionHistoryController;
use Modules\CRM\Http\Controllers\OpportunityController;
use Modules\CRM\Http\Controllers\CommunicationChannelController;
use Modules\CRM\Http\Controllers\ConsentController;
use Modules\CRM\Http\Controllers\MessageController;
use Modules\CRM\Http\Controllers\ProductCatalogController;
use Modules\CRM\Http\Controllers\QuotaController;
use Modules\CRM\Http\Controllers\ServiceTicketController;
use Modules\CRM\Http\Controllers\TerritoryController;

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

Route::prefix('crm')->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('leads', LeadController::class);
    Route::apiResource('opportunities', OpportunityController::class);
    Route::apiResource('activity-logs', ActivityLogController::class);
    Route::apiResource('interaction-histories', InteractionHistoryController::class);
    Route::apiResource('territories', TerritoryController::class);
    Route::apiResource('quotas', QuotaController::class);
    Route::apiResource('product-catalogs', ProductCatalogController::class);
    Route::apiResource('communication-channels', CommunicationChannelController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('consents', ConsentController::class);
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('service-tickets', ServiceTicketController::class);

    Route::prefix('loyalty')->group(function () {
        Route::get('{customerId}/programs/{programId}/balance', [LoyaltyController::class, 'getBalance']);
        Route::post('{customerId}/programs/{programId}/accrue', [LoyaltyController::class, 'accrue']);
        Route::post('{customerId}/programs/{programId}/redeem', [LoyaltyController::class, 'redeem']);
    });
});