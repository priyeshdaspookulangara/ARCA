<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Modules\HR\TimeManagement\Domain\Entities\LeaveRequest;
use Carbon\Carbon;
use Tests\TestCase;

class LeaveRequestApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private Employee $anotherEmployee;
    private LeaveType $paidLeaveType;
    private LeaveType $unpaidLeaveType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
        $this->anotherEmployee = Employee::factory()->create(); // For admin view tests

        $this->paidLeaveType = LeaveType::factory()->create(['is_paid' => true, 'name' => 'Annual Paid Leave']);
        $this->unpaidLeaveType = LeaveType::factory()->create(['is_paid' => false, 'name' => 'Unpaid Personal Leave']);
    }

    private function getValidLeaveRequestData(array $overrides = []): array
    {
        $startDate = Carbon::instance($this->faker->dateTimeBetween('+2 weeks', '+1 month'))->startOfDay();
        $endDate = Carbon::instance($startDate)->addDays($this->faker->numberBetween(0, 4))->endOfDay(); // 1 to 5 days duration

        return array_merge([
            'hr_leave_type_id' => $this->paidLeaveType->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            // 'duration_days' will be calculated by controller or can be specified for half-days
            'reason' => $this->faker->sentence,
            'employee_remarks' => $this->faker->optional()->paragraph,
        ], $overrides);
    }

    public function test_employee_can_submit_leave_request()
    {
        $data = $this->getValidLeaveRequestData();
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/leave-requests", $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'hr_employee_id' => $this->employee->id,
                     'hr_leave_type_id' => $data['hr_leave_type_id'],
                     'status' => LeaveRequest::STATUS_PENDING,
                 ]);
        $this->assertDatabaseHas('hr_leave_requests', [
            'hr_employee_id' => $this->employee->id,
            'reason' => $data['reason']
        ]);
    }

    public function test_submit_leave_request_calculates_duration_excluding_weekends()
    {
        // Example: Friday to Monday (should be 2 working days if no holidays)
        $startDate = Carbon::parse('next friday');
        $endDate = Carbon::parse('next monday');
        $expectedDuration = 2.0;

        $data = $this->getValidLeaveRequestData([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            // duration_days is not sent, should be calculated
        ]);
        unset($data['duration_days']);


        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/leave-requests", $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('hr_leave_requests', [
            'hr_employee_id' => $this->employee->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'duration_days' => $expectedDuration,
        ]);
    }

    public function test_submit_leave_request_uses_provided_duration_for_half_day()
    {
        $startDate = Carbon::parse('next tuesday');
        $data = $this->getValidLeaveRequestData([
            'start_date' => $startDate->toDateString(),
            'end_date' => $startDate->toDateString(), // Same day
            'duration_days' => 0.5,
        ]);

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/leave-requests", $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('hr_leave_requests', [
            'hr_employee_id' => $this->employee->id,
            'duration_days' => 0.5,
        ]);
    }


    public function test_submit_leave_request_fails_for_overlapping_dates()
    {
        $existingRequest = LeaveRequest::factory()->forEmployee($this->employee)->create([
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'status' => LeaveRequest::STATUS_APPROVED,
        ]);

        $data = $this->getValidLeaveRequestData([
            'start_date' => now()->addDays(6)->toDateString(), // Overlaps
            'end_date' => now()->addDays(8)->toDateString(),
        ]);
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/leave-requests", $data);
        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'This leave request overlaps with an existing request.']);
    }

    public function test_employee_can_view_their_own_leave_requests()
    {
        LeaveRequest::factory()->count(2)->forEmployee($this->employee)->create();
        LeaveRequest::factory()->count(1)->forEmployee($this->anotherEmployee)->create(); // For another employee

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/leave-requests");
        $response->assertStatus(200)
                 ->assertJsonCount(2); // Only requests for $this->employee
    }

    public function test_admin_or_manager_can_view_all_leave_requests_with_filters()
    {
        // Assume this endpoint is protected by middleware for admin/manager roles
        LeaveRequest::factory()->count(2)->forEmployee($this->employee)->pending()->create();
        LeaveRequest::factory()->count(1)->forEmployee($this->anotherEmployee)->approved()->create();

        // No filter
        $response = $this->getJson("/api/hr/leave-requests");
        $response->assertStatus(200)->assertJsonCount(3);

        // Filter by status
        $response = $this->getJson("/api/hr/leave-requests?status=" . LeaveRequest::STATUS_PENDING);
        $response->assertStatus(200)->assertJsonCount(2);
    }

    public function test_can_view_a_specific_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->create();
        $response = $this->getJson("/api/hr/leave-requests/{$leaveRequest->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $leaveRequest->id]);
    }

    public function test_employee_can_cancel_their_own_pending_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->pending()->create();
        $updateData = ['status' => LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE];

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE]);
        $this->assertNotNull($leaveRequest->fresh()->cancelled_at);
    }

    public function test_employee_cannot_cancel_approved_request()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->approved()->create();
        $updateData = ['status' => LeaveRequest::STATUS_CANCELLED_BY_EMPLOYEE];

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", $updateData);
        $response->assertStatus(400); // Or 403 if based on policy
    }


    public function test_manager_can_approve_pending_leave_request()
    {
        // Assume this endpoint is protected and the authenticated user is a manager/admin
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->pending()->create();
        $updateData = [
            'status' => LeaveRequest::STATUS_APPROVED,
            'approver_remarks' => 'Approved as requested.',
        ];
        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => LeaveRequest::STATUS_APPROVED]);
        $this->assertNotNull($leaveRequest->fresh()->approved_at);
        // $this->assertEquals(Auth::id(), $leaveRequest->fresh()->approver_user_id); // If auth was mocked
    }

    public function test_manager_can_reject_pending_leave_request()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->pending()->create();
        $updateData = [
            'status' => LeaveRequest::STATUS_REJECTED,
            'rejection_reason' => 'Operational requirements.',
            'approver_remarks' => 'Please apply at a later date.',
        ];
        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", $updateData);
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'status' => LeaveRequest::STATUS_REJECTED,
                     'rejection_reason' => $updateData['rejection_reason']
                 ]);
        $this->assertNotNull($leaveRequest->fresh()->rejected_at);
    }

    public function test_reject_request_requires_rejection_reason()
    {
        $leaveRequest = LeaveRequest::factory()->forEmployee($this->employee)->pending()->create();
        $updateData = ['status' => LeaveRequest::STATUS_REJECTED]; // Missing reason

        $response = $this->putJson("/api/hr/leave-requests/{$leaveRequest->id}", $updateData);
        $response->assertStatus(422)->assertJsonValidationErrors(['rejection_reason']);
    }

}
