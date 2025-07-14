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

    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('periods', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'listPeriods'])->name('periods.list');
        Route::post('periods', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'createPeriod'])->name('periods.create');
        Route::post('periods/{payrollPeriod}/generate-drafts', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'generateDraftPayslips'])->name('periods.generateDrafts');
        Route::get('periods/{payrollPeriod}/payslips', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'listPayslipsForPeriod'])->name('periods.payslips.list');

        Route::get('payslips/{payslip}', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'showPayslip'])->name('payslips.show');
    });
    Route::get('employees/{employee}/payslips', [\Modules\HR\Payroll\Http\Controllers\PayrollController::class, 'listPayslipsForEmployee'])->name('employees.payslips.list');

    // Talent Management - Recruitment
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        // Public endpoint to apply for a job
        Route::post('jobs/{job}/apply', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'apply'])->name('jobs.apply');

        // Admin/Recruiter endpoints for managing applications
        Route::get('applications', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'show'])->name('applications.show');
        Route::put('applications/{application}', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'update'])->name('applications.update');
        Route::delete('applications/{application}', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::get('applications/{application}/resume', [\Modules\HR\TalentManagement\Http\Controllers\JobApplicationController::class, 'downloadResume'])->name('applications.downloadResume');
    });

    // Talent Management - Performance
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('reviews', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'index'])->name('reviews.indexAll');
        Route::get('reviews/{review}', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'show'])->name('reviews.show');
        Route::put('reviews/{review}', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'update'])->name('reviews.update');
        Route::delete('reviews/{review}', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'destroy'])->name('reviews.destroy');
    });
    Route::get('employees/{employee}/performance-reviews', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'index'])->name('employees.reviews.index');
    Route::post('employees/{employee}/performance-reviews', [\Modules\HR\TalentManagement\Http\Controllers\PerformanceReviewController::class, 'store'])->name('employees.reviews.store');


    // Future HR API routes related to Personnel Administration can be added here.
});
