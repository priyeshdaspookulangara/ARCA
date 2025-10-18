<?php

use Illuminate\Support\Facades\Route;
use Modules\GRC\AccessControl\Http\Controllers\RoleController;
use Modules\GRC\AccessControl\Http\Controllers\PermissionController;
use Modules\GRC\AuditMgt\Http\Controllers\AuditLogController;
use Modules\GRC\ProcessControl\Http\Controllers\SoDRuleController;
use Modules\GRC\ComplianceMgt\Http\Controllers\ConsentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('sod-rules', SoDRuleController::class);
Route::apiResource('consents', ConsentController::class);

Route::post('data-request', [ConsentController::class, 'dataRequest']);
Route::post('check-policy', [SoDRuleController::class, 'checkPolicy']);
Route::get('audit/query', [AuditLogController::class, 'query']);