<?php

use Illuminate\Support\Facades\Route;

// Example Fina API Route
// Route::get('/fina/status', [Modules\Fina\Http\Controllers\FinaStatusController::class, 'index']);

Route::get('/fina-status-check', function () {
    return response()->json(['module' => 'Fina', 'status' => 'active_via_api_route']);
});
