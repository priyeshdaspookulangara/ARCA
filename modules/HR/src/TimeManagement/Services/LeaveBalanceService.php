<?php

namespace Modules\HR\TimeManagement\Services;

use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;
use Modules\HR\TimeManagement\Domain\Entities\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveBalanceService
{
    /**
     * Debit the leave balance for an approved leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @return bool True on success, false on failure (e.g., insufficient balance)
     */
    public function debitApprovedLeave(LeaveRequest $leaveRequest): bool
    {
        $fiscalYear = Carbon::parse($leaveRequest->start_date)->year;

        $balance = LeaveBalance::firstOrCreate(
            [
                'hr_employee_id' => $leaveRequest->hr_employee_id,
                'hr_leave_type_id' => $leaveRequest->hr_leave_type_id,
                'fiscal_year' => $fiscalYear,
            ],
            [
                'entitlement_days' => $leaveRequest->leaveType->default_entitlement_days ?? 0,
                'taken_days' => 0,
                'notes' => 'Auto-created due to first leave request of this type in the fiscal year.',
            ]
        );

        if ($balance->balance_days < $leaveRequest->duration_days) {
            // Insufficient balance
            Log::warning("Insufficient leave balance for employee {$leaveRequest->hr_employee_id} for leave type {$leaveRequest->hr_leave_type_id}.");
            return false;
        }

        $balance->taken_days += $leaveRequest->duration_days;
        $balance->save();

        return true;
    }

    /**
     * Credit the leave balance for a cancelled leave request.
     *
     * @param LeaveRequest $leaveRequest
     * @return void
     */
    public function creditCancelledLeave(LeaveRequest $leaveRequest): void
    {
        // Only credit back if the request was previously approved
        if ($leaveRequest->getOriginal('status') !== LeaveRequest::STATUS_APPROVED) {
            return;
        }

        $fiscalYear = Carbon::parse($leaveRequest->start_date)->year;

        $balance = LeaveBalance::where([
            'hr_employee_id' => $leaveRequest->hr_employee_id,
            'hr_leave_type_id' => $leaveRequest->hr_leave_type_id,
            'fiscal_year' => $fiscalYear,
        ])->first();

        if ($balance) {
            $balance->taken_days -= $leaveRequest->duration_days;
            // Ensure taken_days doesn't go below zero
            if ($balance->taken_days < 0) {
                Log::error("Leave balance calculation resulted in negative taken_days for employee {$leaveRequest->hr_employee_id}. Resetting to 0.");
                $balance->taken_days = 0;
            }
            $balance->save();
        } else {
            // This case is unlikely if debit was successful but should be logged if it occurs.
            Log::error("Could not find leave balance to credit for cancelled leave request ID: {$leaveRequest->id}");
        }
    }

    /**
     * Check if an employee has sufficient leave balance for a request.
     *
     * @param LeaveRequest $leaveRequest
     * @return bool
     */
    public function hasSufficientBalance(LeaveRequest $leaveRequest): bool
    {
        $fiscalYear = Carbon::parse($leaveRequest->start_date)->year;

        $balance = LeaveBalance::where([
                'hr_employee_id' => $leaveRequest->hr_employee_id,
                'hr_leave_type_id' => $leaveRequest->hr_leave_type_id,
                'fiscal_year' => $fiscalYear,
            ])->first();

        if (!$balance) {
            // If no balance record exists, check against default entitlement
            $defaultEntitlement = $leaveRequest->leaveType->default_entitlement_days ?? 0;
            return $defaultEntitlement >= $leaveRequest->duration_days;
        }

        return $balance->balance_days >= $leaveRequest->duration_days;
    }
}
