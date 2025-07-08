<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Contract;
use Modules\HR\PersonnelAdmin\Domain\Entities\PersonnelAction;
use Tests\TestCase;

class PersonnelActionTerminationApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private Position $currentPosition;
    private Contract $currentContract;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::factory()->create();
        $job = Job::factory()->create();

        $this->currentPosition = Position::factory()->create([
            'hr_department_id' => $department->id,
            'hr_job_id' => $job->id,
            'is_vacant' => false,
        ]);

        $this->employee = Employee::factory()->create([
            'hr_position_id' => $this->currentPosition->id,
            'hr_department_id' => $department->id,
            'employment_status' => 'active',
        ]);

        $this->currentContract = Contract::factory()->forEmployee($this->employee)->active()->create();
    }

    public function test_can_initiate_termination_action_for_active_employee()
    {
        $terminationData = [
            'termination_type' => 'resignation',
            'effective_date' => now()->addDays(15)->toDateString(),
            'reason' => 'Moving to a new opportunity.',
            'termination_details_notes' => 'Employee provided a formal resignation letter.',
            'is_eligible_for_rehire' => true,
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/terminate", $terminationData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'hr_employee_id' => $this->employee->id,
                'action_type' => PersonnelAction::ACTION_TYPE_TERMINATION,
                'status' => PersonnelAction::STATUS_PENDING,
                'effective_date' => $terminationData['effective_date'],
                'details_json' => [ // Asserting a subset of details_json
                    'termination_type' => 'resignation',
                    'current_hr_position_id' => $this->currentPosition->id,
                    'is_eligible_for_rehire' => true,
                    'notes' => $terminationData['termination_details_notes'],
                ]
            ]);

        $this->assertDatabaseHas('hr_personnel_actions', [
            'hr_employee_id' => $this->employee->id,
            'action_type' => PersonnelAction::ACTION_TYPE_TERMINATION,
            'status' => PersonnelAction::STATUS_PENDING,
        ]);

        // Verify employee's current status, position, and contract have NOT changed yet
        $this->employee->refresh();
        $this->assertEquals('active', $this->employee->employment_status);
        $this->assertEquals($this->currentPosition->id, $this->employee->hr_position_id);
        $this->currentPosition->refresh();
        $this->assertFalse($this->currentPosition->is_vacant); // Position still filled
        $this->currentContract->refresh();
        $this->assertEquals(Contract::STATUS_ACTIVE, $this->currentContract->status);
    }

    public function test_initiate_termination_fails_if_employee_already_terminated()
    {
        $this->employee->update(['employment_status' => 'terminated', 'termination_date' => now()->subDay()]);

        $terminationData = [
            'termination_type' => 'resignation',
            'effective_date' => now()->addDays(15)->toDateString(),
            'reason' => 'Attempting to re-terminate.',
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/terminate", $terminationData);

        $response->assertStatus(400) // Bad Request
                 ->assertJsonFragment(['error' => 'Employee is already terminated.']);
    }

    public function test_initiate_termination_fails_with_missing_required_fields()
    {
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/terminate", []);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['termination_type', 'effective_date', 'reason']);
    }

    public function test_initiate_termination_fails_with_invalid_termination_type()
    {
         $terminationData = [
            'termination_type' => 'invalid_type', // Not in the Rule::in list
            'effective_date' => now()->addDays(15)->toDateString(),
            'reason' => 'Valid reason.',
        ];
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/terminate", $terminationData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['termination_type']);
    }

    public function test_initiate_termination_fails_if_effective_date_is_in_past_not_today()
    {
         $terminationData = [
            'termination_type' => 'resignation',
            'effective_date' => now()->subDay()->toDateString(), // Past date
            'reason' => 'Valid reason.',
        ];
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/terminate", $terminationData);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['effective_date']); // after_or_equal:today
    }
}
