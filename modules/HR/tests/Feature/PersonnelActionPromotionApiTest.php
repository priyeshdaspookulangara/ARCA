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

class PersonnelActionPromotionApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Employee $employee;
    private Position $currentPosition;
    private Position $newVacantPosition;
    private Contract $currentContract;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::factory()->create();
        $job = Job::factory()->create();
        $currentSalary = 60000;

        $this->currentPosition = Position::factory()->create([
            'hr_department_id' => $department->id,
            'hr_job_id' => $job->id,
            'is_vacant' => false, // Initially filled by the employee
        ]);

        $this->employee = Employee::factory()->create([
            'hr_position_id' => $this->currentPosition->id,
            'hr_department_id' => $department->id,
        ]);

        $this->currentContract = Contract::factory()->forEmployee($this->employee)->active()->create([
            'salary_amount' => $currentSalary,
            'salary_frequency' => Contract::FREQUENCY_ANNUAL,
        ]);


        $this->newVacantPosition = Position::factory()->create([
            'hr_department_id' => $department->id, // Can be same or different department
            'hr_job_id' => Job::factory()->create(['job_title' => 'Senior ' . $job->job_title])->id, // A different job
            'is_vacant' => true,
        ]);
    }

    public function test_can_initiate_promotion_action_for_employee()
    {
        $promotionData = [
            'new_hr_position_id' => $this->newVacantPosition->id,
            'new_salary_amount' => $this->currentContract->salary_amount + 10000,
            'new_salary_currency' => $this->currentContract->salary_currency,
            'new_salary_frequency' => $this->currentContract->salary_frequency,
            'effective_date' => now()->addMonth()->toDateString(),
            'reason' => 'Excellent performance and increased responsibilities.',
            'promotion_details_notes' => 'Recommended by department head.',
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/promote", $promotionData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'hr_employee_id' => $this->employee->id,
                'action_type' => PersonnelAction::ACTION_TYPE_PROMOTION,
                'status' => PersonnelAction::STATUS_PENDING,
                'effective_date' => $promotionData['effective_date'],
            ]);

        $this->assertDatabaseHas('hr_personnel_actions', [
            'hr_employee_id' => $this->employee->id,
            'action_type' => PersonnelAction::ACTION_TYPE_PROMOTION,
            'status' => PersonnelAction::STATUS_PENDING,
            'details_json->old_hr_position_id' => $this->currentPosition->id,
            'details_json->new_hr_position_id' => $this->newVacantPosition->id,
            'details_json->new_salary_amount' => $promotionData['new_salary_amount'],
        ]);

        // Verify employee's current position and salary have NOT changed yet
        $this->employee->refresh();
        $this->assertEquals($this->currentPosition->id, $this->employee->hr_position_id);
        $this->assertEquals($this->currentContract->salary_amount, $this->employee->currentContract->salary_amount);
        // Verify new position is still vacant
        $this->assertTrue($this->newVacantPosition->fresh()->is_vacant);

    }

    public function test_initiate_promotion_fails_if_new_position_is_not_vacant()
    {
        $nonVacantPosition = Position::factory()->create(['is_vacant' => false]);
        $promotionData = [
            'new_hr_position_id' => $nonVacantPosition->id,
            'new_salary_amount' => 70000,
            'effective_date' => now()->addMonth()->toDateString(),
            'reason' => 'Promotion attempt to filled position.',
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/promote", $promotionData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_hr_position_id']);
    }

    public function test_initiate_promotion_fails_if_new_position_is_same_as_current()
    {
        $promotionData = [
            'new_hr_position_id' => $this->currentPosition->id, // Same position
            'new_salary_amount' => 70000,
            'effective_date' => now()->addMonth()->toDateString(),
            'reason' => 'Promotion attempt to same position.',
        ];

        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/promote", $promotionData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_hr_position_id']);
    }


    public function test_initiate_promotion_fails_with_missing_required_fields()
    {
        $response = $this->postJson("/api/hr/employees/{$this->employee->id}/promote", []);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_hr_position_id', 'new_salary_amount', 'effective_date', 'reason']);
    }

    public function test_can_list_personnel_actions_for_an_employee()
    {
        PersonnelAction::factory()->count(2)->forEmployee($this->employee)->create();
        PersonnelAction::factory()->create(); // Another action for a different employee

        $response = $this->getJson("/api/hr/employees/{$this->employee->id}/personnel-actions");
        $response->assertStatus(200)
                 ->assertJsonCount(2); // Should only list actions for this employee
    }

    public function test_can_list_all_personnel_actions()
    {
        PersonnelAction::factory()->count(3)->create();
        $response = $this->getJson("/api/hr/personnel-actions");
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }
}
