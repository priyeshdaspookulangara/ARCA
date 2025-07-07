<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\PersonnelAdmin\Http\Controllers\DepartmentController;

/*
|--------------------------------------------------------------------------
| HR Module API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your HR module. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::prefix('hr')->name('hr.api.')->group(function () {
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('jobs', \Modules\HR\PersonnelAdmin\Http\Controllers\JobController::class);
    Route::apiResource('positions', \Modules\HR\PersonnelAdmin\Http\Controllers\PositionController::class);
    Route::apiResource('employees', \Modules\HR\PersonnelAdmin\Http\Controllers\EmployeeController::class);
    // Future HR API routes related to Personnel Administration can be added here.
});
