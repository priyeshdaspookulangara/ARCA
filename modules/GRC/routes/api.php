<?php

use Illuminate\Support\Facades\Route;
use Modules\GRC\Http\Controllers\RoleController;
use Modules\GRC\Http\Controllers\PermissionController;
use Modules\GRC\Http\Controllers\SoDRuleController;
use Modules\GRC\Http\Controllers\ConsentController;
use Modules\GRC\Http\Controllers\AuditLogController;

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('sod-rules', SoDRuleController::class);
Route::apiResource('consents', ConsentController::class);
Route::post('data-request', [ConsentController::class, 'dataRequest']);
Route::post('check-policy', [SoDRuleController::class, 'checkPolicy']);
Route::get('audit/query', [AuditLogController::class, 'query']);