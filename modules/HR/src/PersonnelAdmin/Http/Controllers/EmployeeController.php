<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // For transactions if needed

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::with([
            'position:id,position_title,hr_department_id,hr_job_id',
            'position.department:id,name', // Department through position
            'position.job:id,job_title', // Job through position
            'department:id,name' // Direct department link if stored on employee
        ]);

        if ($request->has('department_id')) {
            $query->where('hr_department_id', $request->department_id)
                  ->orWhereHas('position', function ($q) use ($request) {
                      $q->where('hr_department_id', $request->department_id);
                  });
        }
        if ($request->has('position_id')) {
            $query->where('hr_position_id', $request->position_id);
        }
        if ($request->has('employment_status')) {
            $query->where('employment_status', $request->employment_status);
        }

        $employees = $query->select( // Select specific fields for listing
                            'id', 'employee_id_number', 'first_name', 'last_name', 'work_email',
                            'hr_position_id', 'hr_department_id', 'hire_date', 'employment_status'
                        )
                        ->orderBy('last_name')->orderBy('first_name')->get();
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id_number' => 'required|string|max:50|unique:hr_employees,employee_id_number',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:-18 years',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'personal_email' => 'nullable|email|max:255|unique:hr_employees,personal_email',
            'work_email' => 'required|email|max:255|unique:hr_employees,work_email',
            'phone_mobile' => 'nullable|string|max:50',
            'hire_date' => 'required|date',
            'employment_status' => ['sometimes','string', Rule::in(['active', 'on_leave', 'terminated', 'pending_hire'])],
            'employment_type' => 'nullable|string|max:100',
            'hr_position_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_positions', 'id'),
                // Custom rule to check if position is vacant (if employee is being assigned)
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $position = Position::find($value);
                        if ($position && !$position->is_vacant) {
                            // Allow if position is held by this employee being created (e.g. hire into position)
                            // This logic might be too complex for a simple validator here.
                            // For now, basic check. More advanced logic in service layer.
                            $fail("The selected position (ID: {$value}) is currently not vacant.");
                        }
                    }
                },
            ],
            'hr_department_id' => [ // Storing directly, can also be derived from position
                'nullable',
                'integer',
                Rule::exists('hr_departments', 'id'),
            ],
            // Contract specific fields for initial contract on hire
            'contract_type' => ['required', 'string', Rule::in([Contract::TYPE_PERMANENT, Contract::TYPE_FIXED_TERM, Contract::TYPE_INTERNSHIP, Contract::TYPE_PART_TIME])],
            'contract_start_date' => 'required|date|after_or_equal:hire_date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'salary_amount' => 'required|numeric|min:0',
            'salary_currency' => 'sometimes|string|size:3',
            'salary_frequency' => ['sometimes','string', Rule::in([Contract::FREQUENCY_HOURLY, Contract::FREQUENCY_DAILY, Contract::FREQUENCY_WEEKLY, Contract::FREQUENCY_MONTHLY, Contract::FREQUENCY_ANNUAL])],
            'working_hours_per_week' => 'nullable|numeric|min:0|max:168', // 168 hours in a week
            'probation_period_months' => 'nullable|integer|min:0',
            // Add other fillable fields from Employee model here
        ]);

        // If hr_position_id is provided, ensure hr_department_id matches position's department for consistency
        if (isset($validated['hr_position_id']) && $validated['hr_position_id']) {
            $position = Position::find($validated['hr_position_id']);
            if ($position) {
                // If department_id is also provided, check consistency
                if (isset($validated['hr_department_id']) && $validated['hr_department_id'] != $position->hr_department_id) {
                    return response()->json([
                        'message' => 'The provided department ID does not match the department of the selected position.',
                        'errors' => ['hr_department_id' => ['Department ID does not match the position\'s department.']]
                    ], 422);
                }
                // If department_id is not provided, auto-set it from position
                if (!isset($validated['hr_department_id'])) {
                    $validated['hr_department_id'] = $position->hr_department_id;
                }
            }
        }


        // Transaction for creating employee and updating position
        try {
            DB::beginTransaction();

            $employee = Employee::create($validated);

            // If position is assigned, mark it as not vacant
            $position = null;
            if ($employee->hr_position_id) {
                $position = Position::find($employee->hr_position_id);
                if ($position) {
                    $position->is_vacant = false;
                    $position->save();
                }
            }

            // Create PersonnelAction for 'hire'
            PersonnelAction::create([
                'hr_employee_id' => $employee->id,
                'action_type' => PersonnelAction::ACTION_TYPE_HIRE,
                'effective_date' => $employee->hire_date,
                'details_json' => [
                    'position_id' => $employee->hr_position_id,
                    'department_id' => $employee->hr_department_id,
                    'work_email' => $employee->work_email,
                ],
                'status' => PersonnelAction::STATUS_EXECUTED, // Hire action is typically executed immediately
                'executed_at' => now(),
                // 'created_by_user_id' => auth()->id(), // If auth is set up
            ]);

            // Create initial Contract
            Contract::create([
                'hr_employee_id' => $employee->id,
                'contract_type' => $validated['contract_type'],
                'start_date' => $validated['contract_start_date'],
                'end_date' => $validated['contract_end_date'] ?? null,
                'job_title_snapshot' => $position ? $position->job->job_title : $employee->job_title_placeholder ?? 'N/A', // A placeholder if no position
                'department_snapshot' => $position ? $position->department->name : $employee->department_placeholder ?? 'N/A', // A placeholder
                'salary_amount' => $validated['salary_amount'],
                'salary_currency' => $validated['salary_currency'] ?? 'USD',
                'salary_frequency' => $validated['salary_frequency'] ?? Contract::FREQUENCY_MONTHLY,
                'working_hours_per_week' => $validated['working_hours_per_week'] ?? null,
                'probation_period_months' => $validated['probation_period_months'] ?? null,
                'status' => Contract::STATUS_ACTIVE, // Assuming contract is active upon hire completion
            ]);


            DB::commit();
            return response()->json($employee->load(['position', 'department', 'contracts', 'personnelActions']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e->getMessage()
            return response()->json(['error' => 'Failed to create employee. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     * @param Employee $employee
     * @return JsonResponse
     */
    public function show(Employee $employee): JsonResponse
    {
        return response()->json(
            $employee->load([
                'position.job',
                'position.department',
                'department', // direct department
                // 'manager' // if manager attribute/relationship exists
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'employee_id_number' => ['sometimes','required','string','max:50', Rule::unique('hr_employees', 'employee_id_number')->ignore($employee->id)],
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:-18 years',
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'personal_email' => ['nullable','email','max:255', Rule::unique('hr_employees', 'personal_email')->ignore($employee->id)],
            'work_email' => ['sometimes','required','email','max:255', Rule::unique('hr_employees', 'work_email')->ignore($employee->id)],
            'phone_mobile' => 'nullable|string|max:50',
            'hire_date' => 'sometimes|required|date',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'employment_status' => ['sometimes','string', Rule::in(['active', 'on_leave', 'terminated'])],
            'employment_type' => 'nullable|string|max:100',
            'hr_position_id' => [
                'nullable',
                'integer',
                Rule::exists('hr_positions', 'id'),
                function ($attribute, $value, $fail) use ($request, $employee) {
                    if ($value) {
                        $position = Position::find($value);
                        // Position must be vacant OR held by the current employee
                        if ($position && !$position->is_vacant && $position->currentEmployee && $position->currentEmployee->id !== $employee->id) {
                            $fail("The selected position (ID: {$value}) is currently not vacant or is held by another employee.");
                        }
                    }
                },
            ],
            'hr_department_id' => ['nullable','integer', Rule::exists('hr_departments', 'id')],
        ]);

        // Logic for handling position change: old position becomes vacant, new one becomes filled
        $oldPositionId = $employee->hr_position_id;
        $newPositionId = $request->input('hr_position_id', $oldPositionId); // Use current if not provided

        // If hr_position_id is provided, ensure hr_department_id matches position's department
        if ($request->has('hr_position_id') && $validated['hr_position_id']) {
            $newPosition = Position::find($validated['hr_position_id']);
            if ($newPosition) {
                 if ($request->has('hr_department_id') && $validated['hr_department_id'] != $newPosition->hr_department_id) {
                    return response()->json([
                        'message' => 'The provided department ID does not match the department of the selected position.',
                        'errors' => ['hr_department_id' => ['Department ID does not match the position\'s department.']]
                    ], 422);
                }
                // If department_id is not provided or different, auto-set it from new position
                if (!$request->has('hr_department_id') || (isset($validated['hr_department_id']) && $validated['hr_department_id'] != $newPosition->hr_department_id) ) {
                     $validated['hr_department_id'] = $newPosition->hr_department_id;
                }
            }
        }


        try {
            DB::beginTransaction();
            $employee->update($validated);

            // If position has changed
            if ($newPositionId !== $oldPositionId) {
                // Make old position vacant (if it existed)
                if ($oldPositionId) {
                    $oldPosition = Position::find($oldPositionId);
                    if ($oldPosition) {
                        $oldPosition->is_vacant = true;
                        $oldPosition->save();
                    }
                }
                // Make new position not vacant (if it exists)
                if ($newPositionId) {
                    $newPosition = Position::find($newPositionId);
                    if ($newPosition) {
                        $newPosition->is_vacant = false;
                        $newPosition->save();
                    }
                }
            }
            DB::commit();
            return response()->json($employee->load(['position', 'department']));

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e->getMessage()
            return response()->json(['error' => 'Failed to update employee. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     * @param Employee $employee
     * @return JsonResponse
     */
    public function destroy(Employee $employee): JsonResponse
    {
        try {
            DB::beginTransaction();

            $currentPositionId = $employee->hr_position_id;
            $employee->employment_status = 'terminated'; // Or a more specific status
            $employee->termination_date = now(); // Set termination date
            // $employee->hr_position_id = null; // Optionally unassign position
            $employee->save(); // Save changes before soft deleting
            $employee->delete(); // Soft delete

            // Make the employee's former position vacant
            if ($currentPositionId) {
                $position = Position::find($currentPositionId);
                if ($position) {
                    // Check if any other active employee is assigned to this position (should not happen if 1-to-1)
                    $otherEmployeeOnPosition = Employee::where('hr_position_id', $currentPositionId)
                                                      ->where('id', '!=', $employee->id)
                                                      ->whereNull('termination_date') // active
                                                      ->exists();
                    if (!$otherEmployeeOnPosition) {
                        $position->is_vacant = true;
                        $position->save();
                    }
                }
            }

            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e->getMessage()
            return response()->json(['error' => 'Failed to terminate and delete employee. ' . $e->getMessage()], 500);
        }
    }
}
