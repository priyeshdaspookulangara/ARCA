<?php

use Illuminate\Support\Facades\Route;

// Example CRM API Route
// Route::get('/crm/leads', [Modules\CRM\Sales\Http\LeadsController::class, 'index']);

Route::get('/crm-status-check', function () {
    return response()->json(['module' => 'CRM', 'status' => 'active_via_api_route']);
});
