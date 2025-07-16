<?php

namespace Modules\HR\TimeManagement\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveBalance;
use Carbon\Carbon;

class LeaveBalanceController extends Controller
{
    /**
     * Display a listing of the employee's leave balances.
     * @param Employee $employee
     * @return JsonResponse
     */
    public function index(Request $request, Employee $employee): JsonResponse
    {
        // Add authorization check here: employee can see their own, manager their reports', admin all.

        $fiscalYear = $request->input('fiscal_year', Carbon::now()->year);

        $balances = $employee->leaveBalances()
                              ->where('fiscal_year', $fiscalYear)
                              ->with('leaveType:id,name,is_paid')
                              ->get();

        // Optional: Include leave types for which the employee has entitlement but no balance record yet
        // This makes the list more comprehensive for the user.
        $existingLeaveTypeIds = $balances->pluck('hr_leave_type_id');
        $unlistedLeaveTypes = \Modules\HR\TimeManagement\Domain\Entities\LeaveType::active()
            ->whereNotIn('id', $existingLeaveTypeIds)
            ->whereNotNull('default_entitlement_days')
            ->get();

        $unlistedBalances = $unlistedLeaveTypes->map(function ($leaveType) use ($employee, $fiscalYear) {
            return [
                'id' => null, // No record exists
                'hr_employee_id' => $employee->id,
                'hr_leave_type_id' => $leaveType->id,
                'fiscal_year' => (int)$fiscalYear,
                'entitlement_days' => (float)$leaveType->default_entitlement_days,
                'taken_days' => 0.00,
                'balance_days' => (float)$leaveType->default_entitlement_days,
                'notes' => 'Default entitlement, no record created yet.',
                'leave_type' => [
                    'id' => $leaveType->id,
                    'name' => $leaveType->name,
                    'is_paid' => $leaveType->is_paid,
                ]
            ];
        });

        $allBalances = $balances->concat($unlistedBalances)->sortBy('leave_type.name')->values();

        return response()->json($allBalances);
    }

    /**
     * Manually create or update a leave balance record.
     * Admin-only action.
     * @param Request $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function upsert(Request $request, Employee $employee): JsonResponse
    {
        // Add authorization: if (!Auth::user()->can('manage_leave_balances')) abort(403);

        $validated = $request->validate([
            'hr_leave_type_id' => 'required|integer|exists:hr_leave_types,id',
            'fiscal_year' => 'required|integer|digits:4',
            'entitlement_days' => 'required|numeric|min:0',
            'taken_days' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $balance = LeaveBalance::updateOrCreate(
            [
                'hr_employee_id' => $employee->id,
                'hr_leave_type_id' => $validated['hr_leave_type_id'],
                'fiscal_year' => $validated['fiscal_year'],
            ],
            [
                'entitlement_days' => $validated['entitlement_days'],
                'taken_days' => $validated['taken_days'] ?? 0, // Default to 0 if not provided
                'notes' => ($validated['notes'] ?? '') . ' (Manually updated on ' . now()->toDateString() . ')',
            ]
        );

        return response()->json($balance, 200);
    }
}
