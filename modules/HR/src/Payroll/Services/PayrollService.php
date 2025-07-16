<?php

namespace Modules\HR\Payroll\Services;

use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Modules\HR\Payroll\Domain\Entities\PayslipItem;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Generate draft payslips for all eligible employees for a given period.
     *
     * @param PayrollPeriod $payrollPeriod
     * @return array
     */
    public function generateDraftsForPeriod(PayrollPeriod $payrollPeriod): array
    {
        $processedCount = 0;
        $skippedCount = 0;

        // Find employees who are active at any point during the payroll period
        $eligibleEmployees = Employee::where('employment_status', 'active')
            ->where('hire_date', '<=', $payrollPeriod->end_date)
            ->where(function ($query) use ($payrollPeriod) {
                $query->whereNull('termination_date')
                      ->orWhere('termination_date', '>=', $payrollPeriod->start_date);
            })
            ->with('currentContract') // Eager load the current active contract
            ->get();

        DB::beginTransaction();
        try {
            // First, delete any existing draft payslips for this period to allow re-generation
            Payslip::where('hr_payroll_period_id', $payrollPeriod->id)
                   ->where('status', Payslip::STATUS_DRAFT)
                   ->delete(); // This will cascade delete items due to DB constraints if set up, or do it manually

            $payrollPeriod->update(['status' => PayrollPeriod::STATUS_PROCESSING]);

            foreach ($eligibleEmployees as $employee) {
                // Check if employee already has a non-draft payslip for this period
                $existingPayslip = Payslip::where('hr_employee_id', $employee->id)
                                         ->where('hr_payroll_period_id', $payrollPeriod->id)
                                         ->first();
                if ($existingPayslip) {
                    $skippedCount++;
                    continue;
                }

                $contract = $employee->currentContract;
                if (!$contract) {
                    $skippedCount++;
                    // Log reason for skipping: No active contract found
                    continue;
                }

                // Simplified salary calculation
                $baseSalary = $this->calculateBaseSalary($contract);
                $earnings = [['description' => 'Basic Salary', 'amount' => $baseSalary]];
                // In a real system, you would add other earnings (bonus, overtime, etc.) here.

                $grossSalary = collect($earnings)->sum('amount');

                // Simplified deduction calculation
                $deductions = $this->calculateDeductions($grossSalary);
                $totalDeductions = collect($deductions)->sum('amount');

                $netSalary = $grossSalary - $totalDeductions;

                // Create the Payslip record
                $payslip = Payslip::create([
                    'hr_employee_id' => $employee->id,
                    'hr_payroll_period_id' => $payrollPeriod->id,
                    'gross_salary' => $grossSalary,
                    'total_deductions' => $totalDeductions,
                    'net_salary' => $netSalary,
                    'status' => Payslip::STATUS_DRAFT,
                ]);

                // Create PayslipItem records for earnings
                foreach ($earnings as $earning) {
                    $payslip->items()->create([
                        'item_type' => PayslipItem::TYPE_EARNING,
                        'description' => $earning['description'],
                        'amount' => $earning['amount'],
                        'is_pre_tax' => false, // Earnings are not pre-tax items
                    ]);
                }

                // Create PayslipItem records for deductions
                foreach ($deductions as $deduction) {
                     $payslip->items()->create([
                        'item_type' => PayslipItem::TYPE_DEDUCTION,
                        'description' => $deduction['description'],
                        'amount' => $deduction['amount'],
                        'is_pre_tax' => $deduction['is_pre_tax'],
                    ]);
                }

                $processedCount++;
            }

            // Once done, we might set the period back to open, or to a 'drafts_generated' status
            $payrollPeriod->update(['status' => PayrollPeriod::STATUS_OPEN]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Re-throw exception to be caught by the controller
            throw $e;
        }

        return ['processed' => $processedCount, 'skipped' => $skippedCount];
    }

    /**
     * Calculate base salary for the period.
     * This is a simplified version. A real system would handle proration.
     * @param Contract $contract
     * @return float
     */
    private function calculateProratedSalary(Employee $employee, Contract $contract, PayrollPeriod $payrollPeriod): float
    {
        $monthlySalary = 0;
        switch ($contract->salary_frequency) {
            case Contract::FREQUENCY_ANNUAL:
                $monthlySalary = $contract->salary_amount / 12;
                break;
            case Contract::FREQUENCY_MONTHLY:
                $monthlySalary = (float)$contract->salary_amount;
                break;
            default:
                $monthlySalary = (float)$contract->salary_amount;
        }

        // --- Proration Logic ---
        $periodStart = Carbon::instance($payrollPeriod->start_date);
        $periodEnd = Carbon::instance($payrollPeriod->end_date);
        $hireDate = Carbon::instance($employee->hire_date);
        $terminationDate = $employee->termination_date ? Carbon::instance($employee->termination_date) : null;

        $isNewHireInPeriod = $hireDate->isBetween($periodStart, $periodEnd);
        $isTerminatedInPeriod = $terminationDate && $terminationDate->isBetween($periodStart, $periodEnd);

        if (!$isNewHireInPeriod && !$isTerminatedInPeriod) {
            return round($monthlySalary, 2); // No proration needed
        }

        $totalDaysInMonth = $periodStart->daysInMonth;
        if ($totalDaysInMonth === 0) return 0;

        $effectiveStartDate = $isNewHireInPeriod ? $hireDate : $periodStart;
        $effectiveEndDate = $isTerminatedInPeriod ? $terminationDate : $periodEnd;

        $payableDays = 0;
        if ($effectiveEndDate->gte($effectiveStartDate)) {
            $payableDays = $effectiveStartDate->diffInDays($effectiveEndDate) + 1;
        }

        $payableDays = min($payableDays, $totalDaysInMonth);
        $dailyRate = $monthlySalary / $totalDaysInMonth;
        $proratedSalary = $dailyRate * $payableDays;

        return round($proratedSalary, 2);
    }
        $periodStart = Carbon::instance($payrollPeriod->start_date);
        $periodEnd = Carbon::instance($payrollPeriod->end_date);
        $hireDate = Carbon::instance($employee->hire_date);
        $terminationDate = $employee->termination_date ? Carbon::instance($employee->termination_date) : null;

        // Check if proration is needed
        $isNewHireInPeriod = $hireDate->isBetween($periodStart, $periodEnd);
        $isTerminatedInPeriod = $terminationDate && $terminationDate->isBetween($periodStart, $periodEnd);

        if (!$isNewHireInPeriod && !$isTerminatedInPeriod) {
            return round($monthlySalary, 2); // No proration needed
        }

        $totalDaysInMonth = $periodStart->daysInMonth;
        if ($totalDaysInMonth === 0) return 0; // Avoid division by zero

        $payableDays = 0;
        $effectiveStartDate = $isNewHireInPeriod ? $hireDate : $periodStart;
        $effectiveEndDate = $isTerminatedInPeriod ? $terminationDate : $periodEnd;

        // Calculate payable days within the period
        if ($effectiveEndDate->gte($effectiveStartDate)) {
            $payableDays = $effectiveStartDate->diffInDays($effectiveEndDate) + 1;
        }

        // Ensure payable days doesn't exceed total days in month
        $payableDays = min($payableDays, $totalDaysInMonth);

        $dailyRate = $monthlySalary / $totalDaysInMonth;
        $proratedSalary = $dailyRate * $payableDays;

        return round($proratedSalary, 2);
    }

    /**
     * Calculate deductions.
     * This is a placeholder for a real deduction engine.
     * @param float $grossSalary
     * @return array
     */
    private function calculateDeductions(float $grossSalary): array
    {
        $deductions = [];

        // Placeholder: Flat 10% tax
        $taxAmount = round($grossSalary * 0.10, 2);
        if ($taxAmount > 0) {
            $deductions[] = [
                'description' => 'Income Tax (Placeholder)',
                'amount' => $taxAmount,
                'is_pre_tax' => true, // Tax is a pre-tax deduction in some contexts, simplified here
            ];
        }

        // Placeholder: Fixed $50 for health insurance
        $healthInsurance = 50.00;
        if ($grossSalary > $healthInsurance) {
             $deductions[] = [
                'description' => 'Health Insurance (Placeholder)',
                'amount' => $healthInsurance,
                'is_pre_tax' => true,
            ];
        }

        return $deductions;
    }
}
