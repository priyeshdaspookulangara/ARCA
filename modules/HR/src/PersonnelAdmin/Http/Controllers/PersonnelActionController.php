<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract; // May be needed if promotion affects contract
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PersonnelActionController extends Controller
{
    // Placeholder for listing personnel actions, perhaps for an employee or all
    public function index(Request $request, Employee $employee = null): JsonResponse
    {
        $query = PersonnelAction::with('employee:id,first_name,last_name,employee_id_number');

        if ($employee) {
            $query->where('hr_employee_id', $employee->id);
        }
        // Add other filters like action_type, status, date range etc.
        if ($request->has('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $actions = $query->orderBy('effective_date', 'desc')->orderBy('created_at', 'desc')->get();
        return response()->json($actions);
    }


    /**
     * Initiate a 'Promotion' action for an employee.
     * This currently only creates the PersonnelAction record.
     * Actual updates to Employee and Contract would occur after an approval step (future phase).
     */
    public function initiatePromotion(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'new_hr_position_id' => [
                'required',
                'integer',
                Rule::exists('hr_positions', 'id'),
                Rule::notIn([$employee->hr_position_id]), // Must be a different position
                function ($attribute, $value, $fail) {
                    $position = Position::find($value);
                    if ($position && !$position->is_vacant) {
                        // More complex logic might be needed if the position is "vacant soon" or if an exchange is happening
                        $fail("The selected new position (ID: {$value}) is currently not vacant.");
                    }
                },
            ],
            'new_salary_amount' => 'required|numeric|min:0',
            'new_salary_currency' => 'sometimes|string|size:3',
            // Corrected config access or provide a default array
            'new_salary_frequency' => ['sometimes','string', Rule::in(config('hr.salary_frequencies', [Contract::FREQUENCY_HOURLY, Contract::FREQUENCY_DAILY, Contract::FREQUENCY_WEEKLY, Contract::FREQUENCY_MONTHLY, Contract::FREQUENCY_ANNUAL]))],
            'effective_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:1000',
            'promotion_details_notes' => 'nullable|string',
        ]);

        // Store current details for the action log
        $oldPosition = $employee->position;
        $currentContract = $employee->currentContract; // Assuming currentContract() method exists

        $details = [
            'old_hr_position_id' => $employee->hr_position_id,
            'old_position_title' => $oldPosition ? $oldPosition->position_title : null,
            'old_salary_amount' => $currentContract ? $currentContract->salary_amount : null,
            'old_salary_currency' => $currentContract ? $currentContract->salary_currency : null,
            'old_salary_frequency' => $currentContract ? $currentContract->salary_frequency : null,
            'new_hr_position_id' => $validated['new_hr_position_id'],
            'new_salary_amount' => $validated['new_salary_amount'],
            'new_salary_currency' => $validated['new_salary_currency'] ?? $currentContract->salary_currency ?? 'USD',
            'new_salary_frequency' => $validated['new_salary_frequency'] ?? $currentContract->salary_frequency ?? Contract::FREQUENCY_MONTHLY,
            'notes' => $validated['promotion_details_notes'] ?? null,
        ];

        $personnelAction = PersonnelAction::create([
            'hr_employee_id' => $employee->id,
            'action_type' => PersonnelAction::ACTION_TYPE_PROMOTION,
            'effective_date' => $validated['effective_date'],
            'reason' => $validated['reason'],
            'details_json' => $details,
            'status' => PersonnelAction::STATUS_PENDING, // Promotions typically require approval
            // 'created_by_user_id' => auth()->id(), // If auth is set up
        ]);

        return response()->json($personnelAction, 201);
    }

    // Placeholder for approvePromotion, executePromotion, rejectPromotion methods (future)


    /**
     * Initiate a 'Termination' action for an employee.
     * This currently only creates the PersonnelAction record.
     * Actual updates to Employee and Contract would occur after an approval step (future phase).
     * The EmployeeController::destroy method handles immediate termination. This is for a formal, possibly future-dated process.
     */
    public function initiateTermination(Request $request, Employee $employee): JsonResponse
    {
        if ($employee->employment_status === 'terminated') {
            return response()->json(['error' => 'Employee is already terminated.'], 400);
        }

        $validated = $request->validate([
            'termination_type' => ['required', 'string', Rule::in(['resignation', 'dismissal', 'redundancy', 'contract_ended', 'other'])],
            'effective_date' => 'required|date|after_or_equal:today', // Termination usually effective today or future
            'reason' => 'required|string|max:1000',
            'termination_details_notes' => 'nullable|string',
            'is_eligible_for_rehire' => 'sometimes|boolean',
        ]);

        $details = [
            'termination_type' => $validated['termination_type'],
            'current_hr_position_id' => $employee->hr_position_id,
            'current_position_title' => $employee->position ? $employee->position->position_title : null,
            'current_department_id' => $employee->hr_department_id,
            'current_department_name' => $employee->department ? $employee->department->name : null,
            'is_eligible_for_rehire' => $validated['is_eligible_for_rehire'] ?? true, // Default to true unless specified
            'notes' => $validated['termination_details_notes'] ?? null,
        ];

        $personnelAction = PersonnelAction::create([
            'hr_employee_id' => $employee->id,
            'action_type' => PersonnelAction::ACTION_TYPE_TERMINATION,
            'effective_date' => $validated['effective_date'],
            'reason' => $validated['reason'],
            'details_json' => $details,
            'status' => PersonnelAction::STATUS_PENDING, // Terminations might require approval/exit procedures
            // 'created_by_user_id' => auth()->id(), // If auth is set up
        ]);

        return response()->json($personnelAction, 201);
    }

    // Placeholder for approveTermination, executeTermination methods (future)
}
