<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthMgt\Http\Controllers\RoleController;
use Modules\AuthMgt\Http\Controllers\AuthObjectController;
use Modules\AuthMgt\Http\Controllers\PermissionController;
use Modules\AuthMgt\Http\Controllers\AuditLogController;
use Modules\AuthMgt\Http\Controllers\UserController;
use Modules\AuthMgt\Http\Controllers\UserRoleController;

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

Route::get('/status', function () {
    return response()->json(['module' => 'Authorization Management', 'status' => 'active_via_api_route']);
});

Route::apiResource('roles', RoleController::class);
Route::apiResource('objects', AuthObjectController::class);
Route::post('permissions/assign', [PermissionController::class, 'assign']);
Route::post('permissions/revoke', [PermissionController::class, 'revoke']);
Route::post('permissions/check', [PermissionController::class, 'check']);
Route::get('audit', [AuditLogController::class, 'index']);
Route::get('users', [UserController::class, 'index']);
Route::post('users/{userId}/roles', [UserRoleController::class, 'store']);
Route::delete('users/{userId}/roles/{roleId}', [UserRoleController::class, 'destroy']);