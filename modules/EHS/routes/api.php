<?php

use Illuminate\Support\Facades\Route;

// All routes here are assumed to be under 'api/ehs' prefix and appropriate auth middleware.

Route::get('/status', function () {
    return response()->json(['module' => 'ARCA EHS Main', 'status' => 'active_via_api_route']);
});

// Example Incident Management Routes
// Route::apiResource('incidents', Modules\EHS\IncidentMgt\Http\IncidentController::class);
// Route::post('incidents/{incident}/capa', [Modules\EHS\IncidentMgt\Http\CapaController::class, 'store']);

// Example Risk Assessment Routes
// Route::apiResource('risk-assessments', Modules\EHS\RiskMgt\Http\RiskAssessmentController::class);

// ... and so on for HazMat, WasteMgt, OccHealth, Compliance APIs
