<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Modules\HR\TimeManagement\Domain\Entities\LeaveBalance;
use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;
use Tests\TestCase;

class LeaveBalanceApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private LeaveType $annualLeaveType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
        $this->annualLeaveType = LeaveType::factory()->create(['name' => 'Annual Leave', 'default_entitlement_days' => 20]);

        LeaveBalance::factory()->forEmployee($this->employee)->forLeaveType($this->annualLeaveType)->create([
            'fiscal_year' => now()->year,
            'entitlement_days' => 20,
            'taken_days' => 5,
        ]);
    }

    public function test_approving_leave_request_deducts_from_balance()
    {
        $initialBalance = $this->employee->leaveBalances()->first()->balance_days;
        $this->assertEquals(15, $initialBalance);

        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->ofType($this->annualLeaveType)->pending()->create([
            'duration_days' => 3,
        ]);

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", [
            'status' => LeaveRequest::STATUS_APPROVED,
        ]);

        $response->assertStatus(200);

        $finalBalance = $this->employee->leaveBalances()->first()->balance_days;
        $this->assertEquals(12, $finalBalance); // 15 - 3 = 12
    }

    public function test_approving_leave_request_fails_with_insufficient_balance()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->ofType($this->annualLeaveType)->pending()->create([
            'duration_days' => 20, // More than the 15 days remaining
        ]);

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", [
            'status' => LeaveRequest::STATUS_APPROVED,
        ]);

        $response->assertStatus(422) // Unprocessable Entity
                 ->assertJsonFragment(['error' => 'Insufficient leave balance for this request.']);

        $balance = $this->employee->leaveBalances()->first();
        $this->assertEquals(15, $balance->balance_days); // Balance should be unchanged
    }

    public function test_approving_leave_creates_balance_record_if_not_exists()
    {
        $sickLeaveType = LeaveType::factory()->create(['name' => 'Sick Leave', 'default_entitlement_days' => 10]);
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->ofType($sickLeaveType)->pending()->create([
            'duration_days' => 2,
        ]);

        // Ensure no balance record exists initially
        $this->assertDatabaseMissing('hr_leave_balances', [
            'hr_employee_id' => $this->employee->id,
            'hr_leave_type_id' => $sickLeaveType->id
        ]);

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", [
            'status' => LeaveRequest::STATUS_APPROVED,
        ]);
        $response->assertStatus(200);

        // Verify balance record was created and updated
        $this->assertDatabaseHas('hr_leave_balances', [
            'hr_employee_id' => $this->employee->id,
            'hr_leave_type_id' => $sickLeaveType->id,
            'entitlement_days' => 10,
            'taken_days' => 2,
        ]);
    }

    public function test_cancelling_an_approved_leave_request_credits_balance()
    {
        $initialBalance = $this->employee->leaveBalances()->first()->balance_days;
        $this->assertEquals(15, $initialBalance);

        // First, approve a request to deduct from balance
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->ofType($this->annualLeaveType)->pending()->create([
            'duration_days' => 3,
        ]);
        $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", ['status' => LeaveRequest::STATUS_APPROVED])->assertStatus(200);

        $balanceAfterApproval = $this->employee->leaveBalances()->first()->balance_days;
        $this->assertEquals(12, $balanceAfterApproval);

        // Now, cancel that same approved request
        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", [
            'status' => LeaveRequest::STATUS_CANCELLED_BY_ADMIN,
            'cancellation_reason' => 'Admin cancellation test',
        ]);
        $response->assertStatus(200);

        $finalBalance = $this->employee->leaveBalances()->first()->balance_days;
        $this->assertEquals(15, $finalBalance); // Balance should be restored
    }

    public function test_can_get_employee_leave_balances_for_fiscal_year()
    {
        // Add another balance for the same employee but different type
        $sickLeaveType = LeaveType::factory()->create(['name' => 'Sick Leave', 'default_entitlement_days' => 10]);
        LeaveBalance::factory()->forEmployee($this->employee)->forLeaveType($sickLeaveType)->create(['fiscal_year' => now()->year]);

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/leave-balances?fiscal_year=" . now()->year);

        $response->assertStatus(200)
                 ->assertJsonCount(2); // Annual and Sick leave
    }

    public function test_get_leave_balances_includes_types_with_no_record_but_default_entitlement()
    {
        // This leave type has entitlement but no balance record has been created for the employee yet
        LeaveType::factory()->create(['name' => 'Special Leave', 'default_entitlement_days' => 5]);

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/leave-balances");

        $response->assertStatus(200)
                 ->assertJsonCount(2); // One existing record (Annual) plus the one with default entitlement

        $response->assertJsonFragment(['name' => 'Special Leave', 'balance_days' => 5.00]);
    }

    public function test_admin_can_manually_upsert_a_leave_balance()
    {
        $upsertData = [
            'hr_leave_type_id' => $this->annualLeaveType->id,
            'fiscal_year' => now()->year,
            'entitlement_days' => 25.00, // Giving more entitlement
            'taken_days' => 6.00, // Adjusting taken days
            'notes' => 'Manual adjustment by admin.',
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/leave-balances", $upsertData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('hr_leave_balances', [
            'hr_employee_id' => $this->employee->id,
            'hr_leave_type_id' => $this->annualLeaveType->id,
            'fiscal_year' => now()->year,
            'entitlement_days' => 25.00,
            'taken_days' => 6.00,
        ]);
    }
}
