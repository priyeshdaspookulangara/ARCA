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

    // Personnel Actions
    Route::post('employees/{employee}/promote', [\Modules\HR\PersonnelAdmin\Http\Controllers\PersonnelActionController::class, 'initiatePromotion'])->name('employees.promote');
    Route::post('employees/{employee}/terminate', [\Modules\HR\PersonnelAdmin\Http\Controllers\PersonnelActionController::class, 'initiateTermination'])->name('employees.terminate');
    // Route::post('employees/{employee}/transfer', [\Modules\HR\PersonnelAdmin\Http\Controllers\PersonnelActionController::class, 'initiateTransfer'])->name('employees.transfer');

    // General Personnel Actions listing (maybe admin only)
    Route::get('personnel-actions', [\Modules\HR\PersonnelAdmin\Http\Controllers\PersonnelActionController::class, 'index'])->name('personnelActions.indexAll');
    // Personnel Actions for a specific employee
    Route::get('employees/{employee}/personnel-actions', [\Modules\HR\PersonnelAdmin\Http\Controllers\PersonnelActionController::class, 'index'])->name('employees.personnelActions.index');

    // Contracts
    Route::apiResource('employees.contracts', \Modules\HR\PersonnelAdmin\Http\Controllers\ContractController::class)->shallow();
    // 'shallow' makes /contracts/{contract} available without /employees/{employee} prefix for show, update, destroy
    // So, POST to /employees/{employee}/contracts
    // GET to /employees/{employee}/contracts
    // GET to /contracts/{contract}
    // PUT to /contracts/{contract}
    // DELETE to /contracts/{contract} (though we might not use DELETE, but 'terminate' status change)
    Route::post('contracts/{contract}/terminate', [\Modules\HR\PersonnelAdmin\Http\Controllers\ContractController::class, 'terminate'])->name('contracts.terminate');

    // Time Management - Leave Types (Admin)
    Route::apiResource('leave-types', \Modules\HR\TimeManagement\Http\Controllers\LeaveTypeController::class);

    // Time Management - Leave Requests
    Route::get('leave-requests', [\Modules\HR\TimeManagement\Http\Controllers\LeaveRequestController::class, 'index'])->name('leaveRequests.indexAll'); // Admin/Manager view all
    Route::get('employees/{employee}/leave-requests', [\Modules\HR\TimeManagement\Http\Controllers\LeaveRequestController::class, 'index'])->name('employees.leaveRequests.index'); // Employee views their own
    Route::post('employees/{employee}/leave-requests', [\Modules\HR\TimeManagement\Http\Controllers\LeaveRequestController::class, 'store'])->name('employees.leaveRequests.store');
    Route::get('leave-requests/{leaveRequest}', [\Modules\HR\TimeManagement\Http\Controllers\LeaveRequestController::class, 'show'])->name('leaveRequests.show');
    Route::put('leave-requests/{leaveRequest}', [\Modules\HR\TimeManagement\Http\Controllers\LeaveRequestController::class, 'update'])->name('leaveRequests.update'); // For status changes (approve, reject, cancel)

    // Future HR API routes related to Personnel Administration can be added here.
});
