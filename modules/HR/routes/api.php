<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange\SalaryChangeService;
use Modules\HR\PersonnelAdmin\Application\UseCases\PersonalDataUpdate\PersonalDataUpdateService;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;
use Modules\HR\PersonnelAdmin\Application\UseCases\WorkScheduleChange\WorkScheduleChangeService;
use Modules\HR\PersonnelAdmin\Application\UseCases\LongTermLeave\LongTermLeaveService;
use Modules\HR\OrganizationalManagement\Application\Services\OrganizationalUnitService;
use Modules\HR\OrganizationalManagement\Application\Services\JobService;
use Modules\HR\OrganizationalManagement\Application\Services\PositionService;
use Modules\HR\TimeManagement\Application\Services\TimeRecordService;
use Modules\HR\TimeManagement\Application\Services\AbsenceService;
use Modules\HR\Payroll\Application\Services\PayrollService;
use Modules\HR\Recruitment\Application\Services\RecruitmentService;
use Modules\HR\Benefits\Application\Services\BenefitsService;

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

/*
|--------------------------------------------------------------------------
| Personnel Administration API Routes
|--------------------------------------------------------------------------
*/

Route::post('/employees/{employeeId}/salary', function (Request $request, string $employeeId, SalaryChangeService $salaryChangeService) {
    $newSalary = $request->input('new_salary');
    if (!$newSalary) {
        return response()->json(['error' => 'New salary is required'], 400);
    }
    try {
        $employee = $salaryChangeService->changeSalary($employeeId, (float)$newSalary);
        return response()->json($employee);
    } catch (EmployeeNotFoundException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
});

Route::put('/employees/{employeeId}/personal-data', function (Request $request, string $employeeId, PersonalDataUpdateService $personalDataUpdateService) {
    $data = $request->only(['address', 'marital_status', 'last_name', 'emergency_contact', 'bank_details']);
    if (empty($data)) {
        return response()->json(['error' => 'No data provided for update'], 400);
    }
    try {
        $employee = $personalDataUpdateService->updatePersonalData($employeeId, $data);
        return response()->json($employee);
    } catch (EmployeeNotFoundException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
});

Route::put('/employees/{employeeId}/work-schedule', function (Request $request, string $employeeId, WorkScheduleChangeService $workScheduleChangeService) {
    $data = $request->only(['work_schedule', 'employment_type']);
    if (empty($data)) {
        return response()->json(['error' => 'No data provided for update'], 400);
    }
    try {
        $employee = $workScheduleChangeService->changeWorkSchedule($employeeId, $data);
        return response()->json($employee);
    } catch (EmployeeNotFoundException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
});

Route::post('/employees/{employeeId}/leave/start', function (Request $request, string $employeeId, LongTermLeaveService $longTermLeaveService) {
    $leaveType = $request->input('leave_type');
    if (!$leaveType) {
        return response()->json(['error' => 'Leave type is required'], 400);
    }
    try {
        $employee = $longTermLeaveService->startLeave($employeeId, $leaveType);
        return response()->json($employee);
    } catch (EmployeeNotFoundException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
});

Route::post('/employees/{employeeId}/leave/end', function (Request $request, string $employeeId, LongTermLeaveService $longTermLeaveService) {
    try {
        $employee = $longTermLeaveService->endLeave($employeeId);
        return response()->json($employee);
    } catch (EmployeeNotFoundException $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
});


/*
|--------------------------------------------------------------------------
| Organizational Management API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('om')->group(function () {
    // Organizational Units
    Route::get('/org-units', function (OrganizationalUnitService $service) {
        return response()->json($service->getAllOrganizationalUnits());
    });
    Route::post('/org-units', function (Request $request, OrganizationalUnitService $service) {
        $name = $request->input('name');
        $parentId = $request->input('parent_id');
        if (!$name) {
            return response()->json(['error' => 'Name is required'], 400);
        }
        return response()->json($service->createOrganizationalUnit($name, $parentId), 201);
    });
    Route::get('/org-units/{id}', function (string $id, OrganizationalUnitService $service) {
        $orgUnit = $service->getOrganizationalUnit($id);
        return $orgUnit ? response()->json($orgUnit) : response()->json(['error' => 'Organizational Unit not found'], 404);
    });
    Route::delete('/org-units/{id}', function (string $id, OrganizationalUnitService $service) {
        $service->deleteOrganizationalUnit($id);
        return response()->json(null, 204);
    });

    // Jobs
    Route::get('/jobs', function (JobService $service) {
        return response()->json($service->getAllJobs());
    });
    Route::post('/jobs', function (Request $request, JobService $service) {
        $title = $request->input('title');
        if (!$title) {
            return response()->json(['error' => 'Title is required'], 400);
        }
        return response()->json($service->createJob($title), 201);
    });
    Route::get('/jobs/{id}', function (string $id, JobService $service) {
        $job = $service->getJob($id);
        return $job ? response()->json($job) : response()->json(['error' => 'Job not found'], 404);
    });
    Route::delete('/jobs/{id}', function (string $id, JobService $service) {
        $service->deleteJob($id);
        return response()->json(null, 204);
    });

    // Positions
    Route::get('/positions', function (PositionService $service) {
        return response()->json($service->getAllPositions());
    });
    Route::post('/positions', function (Request $request, PositionService $service) {
        $jobId = $request->input('job_id');
        $orgUnitId = $request->input('org_unit_id');
        if (!$jobId || !$orgUnitId) {
            return response()->json(['error' => 'Job ID and Organizational Unit ID are required'], 400);
        }
        return response()->json($service->createPosition($jobId, $orgUnitId), 201);
    });
    Route::get('/positions/{id}', function (string $id, PositionService $service) {
        $position = $service->getPosition($id);
        return $position ? response()->json($position) : response()->json(['error' => 'Position not found'], 404);
    });
    Route::delete('/positions/{id}', function (string $id, PositionService $service) {
        $service->deletePosition($id);
        return response()->json(null, 204);
    });
});

/*
|--------------------------------------------------------------------------
| Time Management API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('time')->group(function () {
    // Time Records
    Route::get('/employees/{employeeId}/time-records', function (string $employeeId, TimeRecordService $service) {
        return response()->json($service->getTimeRecordsForEmployee($employeeId));
    });
    Route::post('/time-records', function (Request $request, TimeRecordService $service) {
        $employeeId = $request->input('employee_id');
        $date = $request->input('date');
        $hours = $request->input('hours');
        if (!$employeeId || !$date || !$hours) {
            return response()->json(['error' => 'Employee ID, date, and hours are required'], 400);
        }
        return response()->json($service->recordTime($employeeId, $date, (float)$hours), 201);
    });
    Route::post('/time-records/{id}/approve', function (string $id, TimeRecordService $service) {
        $record = $service->approveTimeRecord($id);
        return $record ? response()->json($record) : response()->json(['error' => 'Time record not found'], 404);
    });

    // Absences
    Route::get('/employees/{employeeId}/absences', function (string $employeeId, AbsenceService $service) {
        return response()->json($service->getAbsencesForEmployee($employeeId));
    });
    Route::post('/absences', function (Request $request, AbsenceService $service) {
        $employeeId = $request->input('employee_id');
        $absenceType = $request->input('absence_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (!$employeeId || !$absenceType || !$startDate || !$endDate) {
            return response()->json(['error' => 'Employee ID, absence type, start date, and end date are required'], 400);
        }
        return response()->json($service->requestAbsence($employeeId, $absenceType, $startDate, $endDate), 201);
    });
    Route::post('/absences/{id}/approve', function (string $id, AbsenceService $service) {
        $absence = $service->approveAbsence($id);
        return $absence ? response()->json($absence) : response()->json(['error' => 'Absence not found'], 404);
    });
});

/*
|--------------------------------------------------------------------------
| Payroll API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('payroll')->group(function () {
    Route::post('/run', function (Request $request, PayrollService $service) {
        $startDate = $request->input('period_start_date');
        $endDate = $request->input('period_end_date');
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Period start and end dates are required'], 400);
        }
        $payrollRun = $service->executePayrollRun($startDate, $endDate);
        return response()->json($payrollRun, 201);
    });

    Route::get('/run/{id}', function (string $id, PayrollService $service) {
        $payrollRun = $service->getPayrollRunDetails($id);
        return $payrollRun ? response()->json($payrollRun) : response()->json(['error' => 'Payroll run not found'], 404);
    });

    Route::get('/run/{id}/paychecks', function (string $id, PayrollService $service) {
        $paychecks = $service->getPaychecksForPayrollRun($id);
        return response()->json($paychecks);
    });
});

/*
|--------------------------------------------------------------------------
| Recruitment API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('recruitment')->group(function () {
    // Job Openings
    Route::get('/job-openings', function (RecruitmentService $service) {
        return response()->json($service->getJobOpenings());
    });
    Route::post('/job-openings', function (Request $request, RecruitmentService $service) {
        $positionId = $request->input('position_id');
        if (!$positionId) {
            return response()->json(['error' => 'Position ID is required'], 400);
        }
        return response()->json($service->createJobOpening($positionId), 201);
    });

    // Applications
    Route::get('/job-openings/{id}/applications', function (string $id, RecruitmentService $service) {
        return response()->json($service->getApplicationsForJobOpening($id));
    });
    Route::post('/job-openings/{id}/applications', function (Request $request, string $id, RecruitmentService $service) {
        $applicantData = $request->only(['first_name', 'last_name', 'email', 'phone']);
        if (empty($applicantData['first_name']) || empty($applicantData['last_name']) || empty($applicantData['email'])) {
            return response()->json(['error' => 'First name, last name, and email are required'], 400);
        }
        return response()->json($service->submitApplication($id, $applicantData), 201);
    });
    Route::put('/applications/{id}', function (Request $request, string $id, RecruitmentService $service) {
        $status = $request->input('status');
        if (!$status) {
            return response()->json(['error' => 'Status is required'], 400);
        }
        $application = $service->updateApplicationStatus($id, $status);
        return $application ? response()->json($application) : response()->json(['error' => 'Application not found'], 404);
    });
});

/*
|--------------------------------------------------------------------------
| Benefits API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('benefits')->group(function () {
    // Benefit Plans
    Route::get('/plans', function (BenefitsService $service) {
        return response()->json($service->getBenefitPlans());
    });
    Route::post('/plans', function (Request $request, BenefitsService $service) {
        $name = $request->input('name');
        $type = $request->input('type');
        $deductionAmount = $request->input('deduction_amount');
        if (!$name || !$type || !$deductionAmount) {
            return response()->json(['error' => 'Name, type, and deduction amount are required'], 400);
        }
        return response()->json($service->createBenefitPlan($name, $type, (float)$deductionAmount), 201);
    });

    // Employee Enrollments
    Route::get('/employees/{employeeId}/enrollments', function (string $employeeId, BenefitsService $service) {
        return response()->json($service->getEmployeeEnrollments($employeeId));
    });
    Route::post('/enrollments', function (Request $request, BenefitsService $service) {
        $employeeId = $request->input('employee_id');
        $planId = $request->input('plan_id');
        if (!$employeeId || !$planId) {
            return response()->json(['error' => 'Employee ID and Plan ID are required'], 400);
        }
        return response()->json($service->enrollEmployeeInBenefit($employeeId, $planId), 201);
    });
});