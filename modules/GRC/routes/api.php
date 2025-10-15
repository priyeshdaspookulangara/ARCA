<?php

use Illuminate\Support\Facades\Route;

// All routes here are assumed to be under 'api/grc' prefix and appropriate admin auth middleware.

Route::get('/status', function () {
    return response()->json(['module' => 'ARCA GRC Main', 'status' => 'active_via_api_route']);
});

// Example Access Control Routes
// Route::post('/sod-analysis/run', [Modules\GRC\AccessControl\Http\SoDAnalysisController::class, 'runAnalysis']);
// Route::get('/user-provisioning/requests', [Modules\GRC\AccessControl\Http\UserProvisioningController::class, 'index']);

// Example Process Control Routes
// Route::apiResource('internal-controls', Modules\GRC\ProcessControl\Http\InternalControlController::class);
// Route::apiResource('ccm-exceptions', Modules\GRC\ProcessControl\Http\CcmExceptionController::class);

// ... and so on for RiskMgt, AuditMgt, ComplianceMgt APIs
