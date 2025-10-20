<?php

use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['module' => 'ARCA PLM Main', 'status' => 'active_via_api_route']);
});

// Route::apiResource('items', Modules\PLM\PDM\Http\ItemController::class);
// Route::apiResource('items.versions', Modules\PLM\PDM\Http\ItemVersionController::class)->shallow();
// Route::get('items/{itemId}/versions/{versionId}/bom', [Modules\PLM\BOM\Http\BomController::class, 'show']);

// Route::apiResource('ecr', Modules\PLM\ChangeMgt\Http\EcrController::class);
// Route::apiResource('eco', Modules\PLM\ChangeMgt\Http\EcoController::class);

// Route::apiResource('documents', Modules\PLM\DocumentMgt\Http\DocumentController::class);