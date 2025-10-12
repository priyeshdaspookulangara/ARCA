<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HR\TalentManagement\Application\Services\TalentManagementService;

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
| Talent Management API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('talent')->group(function () {
    // Performance Reviews
    Route::get('/employees/{employeeId}/performance-reviews', function (string $employeeId, TalentManagementService $service) {
        return response()->json($service->getPerformanceReviewsForEmployee($employeeId));
    });
    Route::post('/performance-reviews', function (Request $request, TalentManagementService $service) {
        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (!$employeeId || !$startDate || !$endDate) {
            return response()->json(['error' => 'Employee ID, start date, and end date are required'], 400);
        }
        return response()->json($service->createPerformanceReview($employeeId, $startDate, $endDate), 201);
    });
    Route::put('/performance-reviews/{id}', function (Request $request, string $id, TalentManagementService $service) {
        $rating = $request->input('rating');
        $comments = $request->input('comments');
        if (!$rating || !$comments) {
            return response()->json(['error' => 'Rating and comments are required'], 400);
        }
        $review = $service->completePerformanceReview($id, (int)$rating, $comments);
        return $review ? response()->json($review) : response()->json(['error' => 'Performance review not found'], 404);
    });

    // Goals
    Route::get('/employees/{employeeId}/goals', function (string $employeeId, TalentManagementService $service) {
        return response()->json($service->getGoalsForEmployee($employeeId));
    });
    Route::post('/goals', function (Request $request, TalentManagementService $service) {
        $employeeId = $request->input('employee_id');
        $description = $request->input('description');
        if (!$employeeId || !$description) {
            return response()->json(['error' => 'Employee ID and description are required'], 400);
        }
        return response()->json($service->createGoal($employeeId, $description), 201);
    });
    Route::put('/goals/{id}', function (Request $request, string $id, TalentManagementService $service) {
        $status = $request->input('status');
        if (!$status) {
            return response()->json(['error' => 'Status is required'], 400);
        }
        $goal = $service->updateGoalStatus($id, $status);
        return $goal ? response()->json($goal) : response()->json(['error' => 'Goal not found'], 404);
    });
});