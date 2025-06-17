<?php

use Illuminate\Support\Facades\Route;

Route::get('/ewm-status-check', function () {
    return response()->json(['module' => 'ARCA EWM Main', 'status' => 'active_via_api_route']);
});

// Desktop/Monitoring API routes
Route::get('/monitor/tasks', function() { /* ... */ });
Route::get('/stock-overview', function() { /* ... */ });

// Specific API routes for sub-domains like Inbound, Outbound can be included here
// or loaded by the EwmServiceProvider from their respective domain directories.
// Example:
// Route::group(['prefix' => 'inbound', 'namespace' => 'Modules\EWM\Inbound\Http\Controllers'], function() {
//     // require module_path('EWM', 'src/Inbound/Http/api_routes.php'); // If routes are in domain
// });
