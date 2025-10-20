<?php

use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['module' => 'ARCA EHS Main', 'status' => 'active_via_api_route']);
});

// Route::apiResource('incidents', Modules\EHS\IncidentMgt\Http\IncidentController::class);
// Route::post('incidents/{incident}/capa', [Modules\EHS\IncidentMgt\Http\CapaController::class, 'store']);

// Route::apiResource('risk-assessments', Modules\EHS\RiskMgt\Http\RiskAssessmentController::class);