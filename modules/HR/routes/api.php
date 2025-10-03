<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange\SalaryChangeService;
use Modules\HR\PersonnelAdmin\Application\UseCases\PersonalDataUpdate\PersonalDataUpdateService;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;

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