<?php

use Illuminate\Support\Facades\Route;

Route::get('/ewm-status-check', function () {
    return response()->json(['module' => 'Extended Warehouse Management', 'status' => 'active_via_api_route']);
});

Route::get('/monitor/tasks', function() { /* ... */ });
Route::get('/stock-overview', function() { /* ... */ });

// Route::group(['prefix' => 'inbound', 'namespace' => 'Modules\EWM\Inbound\Http\Controllers'], function() {
//     Route::apiResource('deliveries', 'InboundDeliveryController');
// });