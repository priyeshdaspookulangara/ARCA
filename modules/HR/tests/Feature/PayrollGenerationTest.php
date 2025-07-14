<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Tests\TestCase;

class PayrollGenerationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private PayrollPeriod $openPeriod;
    private Employee $activeEmployeeWithContract;
    private Employee $activeEmployeeWithoutContract;
    private Employee $inactiveEmployee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->openPeriod = PayrollPeriod::factory()->open()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Employee 1: Active, has a contract, should be processed
        $this->activeEmployeeWithContract = Employee::factory()->create(['employment_status' => 'active']);
        Contract::factory()->forEmployee($this->activeEmployeeWithContract)->active()->create([
            'salary_amount' => 60000,
            'salary_frequency' => Contract::FREQUENCY_ANNUAL, // $5000/month
        ]);

        // Employee 2: Active, but no contract, should be skipped
        $this->activeEmployeeWithoutContract = Employee::factory()->create(['employment_status' => 'active']);

        // Employee 3: Terminated, should be skipped
        $this->inactiveEmployee = Employee::factory()->create(['employment_status' => 'terminated']);
        Contract::factory()->forEmployee($this->inactiveEmployee)->create();
    }

    public function test_can_generate_draft_payslips_for_eligible_employees()
    {
        $response = $this->postJson("/api/hr/payroll-periods/{$this->openPeriod->id}/generate-drafts");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Draft payslip generation process initiated.',
                     'employees_processed' => 1,
                     'employees_skipped' => 2, // One without contract, one inactive
                 ]);

        // Verify a payslip was created for the eligible employee
        $this->assertDatabaseHas('hr_payslips', [
            'hr_employee_id' => $this->activeEmployeeWithContract->id,
            'hr_payroll_period_id' => $this->openPeriod->id,
            'status' => Payslip::STATUS_DRAFT,
        ]);

        // Verify payslip calculation (based on simplified service logic)
        $payslip = Payslip::where('hr_employee_id', $this->activeEmployeeWithContract->id)->first();
        $this->assertNotNull($payslip);
        $this->assertEquals(5000.00, $payslip->gross_salary); // 60000 / 12
        // Based on service placeholders: Tax (10% of 5000 = 500) + Insurance (50) = 550
        $this->assertEquals(550.00, $payslip->total_deductions);
        $this->assertEquals(4450.00, $payslip->net_salary);

        // Verify payslip items were created
        $this->assertDatabaseHas('hr_payslip_items', ['hr_payslip_id' => $payslip->id, 'item_type' => 'earning', 'description' => 'Basic Salary', 'amount' => 5000.00]);
        $this->assertDatabaseHas('hr_payslip_items', ['hr_payslip_id' => $payslip->id, 'item_type' => 'deduction', 'description' => 'Income Tax (Placeholder)', 'amount' => 500.00]);
        $this->assertDatabaseHas('hr_payslip_items', ['hr_payslip_id' => $payslip->id, 'item_type' => 'deduction', 'description' => 'Health Insurance (Placeholder)', 'amount' => 50.00]);


        // Verify no payslip was created for the other employees
        $this->assertDatabaseMissing('hr_payslips', ['hr_employee_id' => $this->activeEmployeeWithoutContract->id]);
        $this->assertDatabaseMissing('hr_payslips', ['hr_employee_id' => $this->inactiveEmployee->id]);
    }

    public function test_generate_drafts_deletes_old_drafts_before_creating_new_ones()
    {
        // Create an old draft payslip for the employee
        $oldPayslip = Payslip::factory()->draft()->forEmployee($this->activeEmployeeWithContract)->forPeriod($this->openPeriod)->create(['gross_salary' => 1.00]);

        $response = $this->postJson("/api/hr/payroll-periods/{$this->openPeriod->id}/generate-drafts");
        $response->assertStatus(200);

        // Verify the old payslip is gone
        $this->assertDatabaseMissing('hr_payslips', ['id' => $oldPayslip->id]);

        // Verify the new correct payslip exists
        $this->assertDatabaseHas('hr_payslips', [
            'hr_employee_id' => $this->activeEmployeeWithContract->id,
            'hr_payroll_period_id' => $this->openPeriod->id,
            'gross_salary' => 5000.00,
        ]);
        $this->assertEquals(1, Payslip::where('hr_employee_id', $this->activeEmployeeWithContract->id)->count());
    }

    public function test_cannot_generate_payslips_for_non_open_period()
    {
        $closedPeriod = PayrollPeriod::factory()->create(['status' => PayrollPeriod::STATUS_CLOSED]);
        $response = $this->postJson("/api/hr/payroll-periods/{$closedPeriod->id}/generate-drafts");
        $response->assertStatus(400)
                 ->assertJsonFragment(['error' => 'Payroll can only be generated for periods with "open" status.']);
    }
}
