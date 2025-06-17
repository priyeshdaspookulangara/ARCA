<?php

use Illuminate\Support\Facades\Route;

// All routes here are assumed to be under 'api/mdg' prefix and appropriate auth middleware.

Route::get('/status', function () {
    return response()->json(['module' => 'ARCA MDG Main', 'status' => 'active_via_api_route']);
});

// Example Change Request Routes
// Route::apiResource('change-requests', Modules\MDG\ChangeRequestMgt\Http\ChangeRequestController::class);

// Example Workflow Task Routes
// Route::get('workflow-tasks/my-pending', [Modules\MDG\WorkflowEngine\Http\WorkflowTaskController::class, 'getMyPending']);
// Route::post('workflow-tasks/{task}/complete', [Modules\MDG\WorkflowEngine\Http\WorkflowTaskController::class, 'complete']);

// Example Master Data Object Routes (e.g., for searching active data)
// Route::get('materials', [Modules\MDG\MasterDataObjects\Material\Http\MaterialController::class, 'index']);
// Route::get('business-partners/{bpId}', [Modules\MDG\MasterDataObjects\BusinessPartner\Http\BusinessPartnerController::class, 'show']);

EOL
