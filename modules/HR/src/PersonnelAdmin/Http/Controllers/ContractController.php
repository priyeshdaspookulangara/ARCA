<?php

namespace Modules\HR\PersonnelAdmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction; // For logging contract changes
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    /**
     * Display a listing of the employee's contracts.
     * @param Employee $employee
     * @return JsonResponse
     */
    public function index(Employee $employee): JsonResponse
    {
        $contracts = $employee->contracts()->orderBy('start_date', 'desc')->get();
        return response()->json($contracts);
    }

    /**
     * Store a newly created contract for an employee.
     * This might supersede existing active contracts.
     * @param Request $request
     * @param Employee $employee
     * @return JsonResponse
     */
    public function store(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'contract_type' => ['required', 'string', Rule::in(array_keys(config('hr.contract_types', [
                Contract::TYPE_PERMANENT => 'Permanent',
                Contract::TYPE_FIXED_TERM => 'Fixed-Term',
                Contract::TYPE_INTERNSHIP => 'Internship',
                Contract::TYPE_PART_TIME => 'Part-Time',
            ])))],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'job_title_snapshot' => 'required|string|max:255',
            'department_snapshot' => 'nullable|string|max:255',
            'salary_amount' => 'required|numeric|min:0',
            'salary_currency' => 'sometimes|string|size:3',
            'salary_frequency' => ['required','string', Rule::in(array_keys(config('hr.salary_frequencies', [
                Contract::FREQUENCY_HOURLY => 'Hourly',
                Contract::FREQUENCY_DAILY => 'Daily',
                Contract::FREQUENCY_WEEKLY => 'Weekly',
                Contract::FREQUENCY_MONTHLY => 'Monthly',
                Contract::FREQUENCY_ANNUAL => 'Annual',
            ])))],
            'working_hours_per_week' => 'nullable|numeric|min:0|max:168',
            'probation_period_months' => 'nullable|integer|min:0',
            'notice_period_days' => 'nullable|integer|min:0',
            'contract_document_path' => 'nullable|string|max:1024',
            'status' => ['sometimes','string', Rule::in(array_keys(config('hr.contract_statuses', [
                Contract::STATUS_PENDING_SIGNATURE => 'Pending Signature',
                Contract::STATUS_ACTIVE => 'Active',
            ])))], // Typically new contracts are pending or active
            'remarks' => 'nullable|string',
        ]);

        // Default status if not provided
        $validated['status'] = $validated['status'] ?? Contract::STATUS_PENDING_SIGNATURE;
        $validated['salary_currency'] = $validated['salary_currency'] ?? 'USD';


        try {
            DB::beginTransaction();

            // If the new contract is active, potentially supersede other active ones
            if ($validated['status'] === Contract::STATUS_ACTIVE) {
                $employee->contracts()
                         ->where('status', Contract::STATUS_ACTIVE)
                         ->where('id', '!=', null) // placeholder to avoid error on no existing contracts
                         ->update(['status' => Contract::STATUS_SUPERSEDED, 'end_date' => DB::raw('LEAST(end_date, \'' . $validated['start_date'] . '\')')]);
            }

            $contract = $employee->contracts()->create($validated);

            // Log a personnel action for contract update/creation
            PersonnelAction::create([
                'hr_employee_id' => $employee->id,
                'action_type' => PersonnelAction::ACTION_TYPE_CONTRACT_UPDATE, // Or a more specific type like 'new_contract'
                'effective_date' => $contract->start_date,
                'details_json' => [
                    'contract_id' => $contract->id,
                    'contract_type' => $contract->contract_type,
                    'salary_amount' => $contract->salary_amount,
                    'status' => $contract->status,
                ],
                'status' => PersonnelAction::STATUS_EXECUTED, // Assuming direct execution for now
                'executed_at' => now(),
            ]);

            DB::commit();
            return response()->json($contract, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create contract. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified contract.
     * @param Contract $contract
     * @return JsonResponse
     */
    public function show(Contract $contract): JsonResponse
    {
        // Ensure the contract belongs to the employee if employee is part of the route, or use global contract ID.
        // For this example, assuming direct access by contract ID is fine if authorized.
        return response()->json($contract->load('employee:id,first_name,last_name'));
    }

    /**
     * Update the specified contract.
     * Some fields might be immutable or require a new contract (e.g. major salary change, type change).
     * This example allows updating most fields.
     * @param Request $request
     * @param Contract $contract
     * @return JsonResponse
     */
    public function update(Request $request, Contract $contract): JsonResponse
    {
        $validated = $request->validate([
            'contract_type' => ['sometimes','required', 'string', Rule::in(array_keys(config('hr.contract_types')))],
            'start_date' => 'sometimes|required|date',
            'end_date' => 'nullable|date|after:start_date',
            'job_title_snapshot' => 'sometimes|required|string|max:255',
            'department_snapshot' => 'nullable|string|max:255',
            'salary_amount' => 'sometimes|required|numeric|min:0',
            'salary_currency' => 'sometimes|string|size:3',
            'salary_frequency' => ['sometimes','required','string', Rule::in(array_keys(config('hr.salary_frequencies')))],
            'working_hours_per_week' => 'nullable|numeric|min:0|max:168',
            'probation_period_months' => 'nullable|integer|min:0',
            'notice_period_days' => 'nullable|integer|min:0',
            'contract_document_path' => 'nullable|string|max:1024',
            'status' => ['sometimes','required','string', Rule::in(array_keys(config('hr.contract_statuses')))],
            'remarks' => 'nullable|string',
        ]);

        $oldDetails = $contract->toArray(); // For logging changes

        try {
            DB::beginTransaction();

            // If status is changing to active, and different from current, supersede others.
            if (isset($validated['status']) && $validated['status'] === Contract::STATUS_ACTIVE && $contract->status !== Contract::STATUS_ACTIVE) {
                 $contract->employee->contracts()
                         ->where('status', Contract::STATUS_ACTIVE)
                         ->where('id', '!=', $contract->id)
                         ->update(['status' => Contract::STATUS_SUPERSEDED, 'end_date' => DB::raw('LEAST(IFNULL(end_date, CURDATE()), \'' . ($validated['start_date'] ?? $contract->start_date) . '\')')]);
            }

            $contract->update($validated);

            // Log a personnel action for contract update
            PersonnelAction::create([
                'hr_employee_id' => $contract->hr_employee_id,
                'action_type' => PersonnelAction::ACTION_TYPE_CONTRACT_UPDATE,
                'effective_date' => $contract->start_date, // Or a specific "change_effective_date"
                'details_json' => [
                    'contract_id' => $contract->id,
                    'updated_fields' => array_keys($validated),
                    'old_status' => $oldDetails['status'] ?? null, // Example of logging specific old values
                    'new_status' => $contract->status,
                ],
                'status' => PersonnelAction::STATUS_EXECUTED,
                'executed_at' => now(),
            ]);

            DB::commit();
            return response()->json($contract);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update contract. ' . $e->getMessage()], 500);
        }
    }

    /**
     * "Terminate" a contract (mark as terminated_early).
     * This is a status change, not a hard delete.
     * @param Request $request
     * @param Contract $contract
     * @return JsonResponse
     */
    public function terminate(Request $request, Contract $contract): JsonResponse
    {
        if (!in_array($contract->status, [Contract::STATUS_ACTIVE, Contract::STATUS_PENDING_SIGNATURE])) {
            return response()->json(['error' => 'Only active or pending signature contracts can be terminated early.'], 400);
        }

        $validated = $request->validate([
            'termination_reason' => 'required|string|max:1000',
            'termination_date' => 'required|date|after_or_equal:'.$contract->start_date,
        ]);

        try {
            DB::beginTransaction();

            $contract->status = Contract::STATUS_TERMINATED_EARLY;
            $contract->end_date = $validated['termination_date']; // Set the actual end date
            $contract->remarks = ($contract->remarks ? $contract->remarks . "\n" : '') . "Terminated early. Reason: " . $validated['termination_reason'];
            $contract->save();

            // Log a personnel action for contract termination
            PersonnelAction::create([
                'hr_employee_id' => $contract->hr_employee_id,
                'action_type' => 'contract_termination', // Could be a specific action type
                'effective_date' => $validated['termination_date'],
                'details_json' => [
                    'contract_id' => $contract->id,
                    'reason' => $validated['termination_reason'],
                ],
                'status' => PersonnelAction::STATUS_EXECUTED,
                'executed_at' => now(),
            ]);

            // Potentially trigger logic to check if employee needs a new contract or if employment status changes

            DB::commit();
            return response()->json($contract);
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to terminate contract. ' . $e->getMessage()], 500);
        }
    }
}
