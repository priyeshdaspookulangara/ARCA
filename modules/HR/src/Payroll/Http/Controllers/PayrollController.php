<?php

namespace Modules\HR\Payroll\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\Payroll\Services\PayrollService; // Assuming a service class for logic
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * List all payroll periods.
     */
    public function listPeriods(Request $request): JsonResponse
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();
        return response()->json($periods);
    }

    /**
     * Create a new payroll period.
     */
    public function createPeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payment_date' => 'required|date|after_or_equal:end_date',
        ]);

        $period = PayrollPeriod::create($validated);
        return response()->json($period, 201);
    }

    /**
     * Generate draft payslips for all eligible employees in a given period.
     */
    public function generateDraftPayslips(PayrollPeriod $payrollPeriod): JsonResponse
    {
        if ($payrollPeriod->status !== PayrollPeriod::STATUS_OPEN) {
            return response()->json(['error' => 'Payroll can only be generated for periods with "open" status.'], 400);
        }

        try {
            $result = $this->payrollService->generateDraftsForPeriod($payrollPeriod);
            return response()->json([
                'message' => 'Draft payslip generation process initiated.',
                'employees_processed' => $result['processed'],
                'employees_skipped' => $result['skipped'],
            ]);
        } catch (\Exception $e) {
            // Log the error $e->getMessage()
            return response()->json(['error' => 'An error occurred during payslip generation.'], 500);
        }
    }

    /**
     * List all payslips for a given period.
     */
    public function listPayslipsForPeriod(Request $request, PayrollPeriod $payrollPeriod): JsonResponse
    {
        $payslips = $payrollPeriod->payslips()->with('employee:id,first_name,last_name,employee_id_number')->get();
        return response()->json($payslips);
    }

    /**
     * Get a specific payslip with its items.
     */
    public function showPayslip(Payslip $payslip): JsonResponse
    {
        // Add authorization checks here
        return response()->json($payslip->load(['items', 'employee:id,first_name,last_name', 'payrollPeriod']));
    }

    /**
     * List all payslips for a specific employee.
     */
    public function listPayslipsForEmployee(Employee $employee): JsonResponse
    {
        // Add authorization checks here
        $payslips = $employee->payslips()->with('payrollPeriod:id,name,payment_date')->orderBy('hr_payroll_period_id', 'desc')->get();
        return response()->json($payslips);
    }
}
