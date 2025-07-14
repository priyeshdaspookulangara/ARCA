<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\Payroll\Domain\Entities\PayrollPeriod;
use Modules\HR\Payroll\Domain\Entities\Payslip;
use Tests\TestCase;

class PayrollDataApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private PayrollPeriod $period1;
    private PayrollPeriod $period2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
        $this->period1 = PayrollPeriod::factory()->create(['name' => 'Jan 2024']);
        $this->period2 = PayrollPeriod::factory()->create(['name' => 'Feb 2024']);

        // Create payslips for the employee for both periods
        Payslip::factory()->forEmployee($this->employee)->forPeriod($this->period1)->create();
        Payslip::factory()->forEmployee($this->employee)->forPeriod($this->period2)->create();

        // Create a payslip for another employee in period 1
        Payslip::factory()->forPeriod($this->period1)->create();
    }

    public function test_can_list_all_payroll_periods()
    {
        $response = $this->getJson('/api/hr/payroll/periods');
        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    public function test_can_create_a_payroll_period()
    {
        $data = [
            'name' => 'March 2024',
            'start_date' => '2024-03-01',
            'end_date' => '2024-03-31',
            'payment_date' => '2024-04-05',
        ];
        $response = $this->postJson('/api/hr/payroll/periods', $data);
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'March 2024']);
        $this->assertDatabaseHas('hr_payroll_periods', ['name' => 'March 2024']);
    }

    public function test_can_list_all_payslips_for_a_given_period()
    {
        $response = $this->getJson("/api/hr/payroll/periods/{$this->period1->id}/payslips");
        $response->assertStatus(200)
                 ->assertJsonCount(2); // The one for our main employee, and the one for the other employee
    }

    public function test_can_list_all_payslips_for_an_employee()
    {
        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/payslips");
        $response->assertStatus(200)
                 ->assertJsonCount(2); // Jan and Feb for our main employee
    }

    public function test_can_get_a_specific_payslip_with_items()
    {
        $payslip = Payslip::factory()->forEmployee($this->employee)->forPeriod($this->period1)->create();
        \Modules\HR\Payroll\Domain\Entities\PayslipItem::factory()->count(3)->forPayslip($payslip)->earning()->create();
        \Modules\HR\Payroll\Domain\Entities\PayslipItem::factory()->count(2)->forPayslip($payslip)->deduction()->create();

        $response = $this->getJson("/api/hr/payroll/payslips/{$payslip->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $payslip->id])
                 ->assertJsonCount(5, 'items'); // Check that the items are loaded
    }
}
