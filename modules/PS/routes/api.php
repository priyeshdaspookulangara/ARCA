<?php

use Illuminate\Support\Facades\Route;

Route::get('/ps-status-check', function () {
    return response()->json(['module' => 'Project System Main', 'status' => 'active_via_api_route']);
});

// Further routes will be organized and potentially loaded from sub-domain route files
// by the PsServiceProvider or included here.
// Example:
// Route::apiResource('projects', Modules\PS\Structuring\Http\ProjectDefinitionController::class);
