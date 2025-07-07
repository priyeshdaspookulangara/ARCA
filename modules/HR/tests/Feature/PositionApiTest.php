<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Tests\TestCase;

class PositionApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Job $job;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->job = Job::factory()->create();
        $this->department = Department::factory()->create();
    }

    private function getValidPositionData(array $overrides = []): array
    {
        return array_merge([
            'position_title' => $this->faker->unique()->jobTitle . ' - ' . $this->faker->word,
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
            'description' => $this->faker->sentence,
            'is_vacant' => true,
            'effective_date_start' => now()->subMonth()->toDateString(),
        ], $overrides);
    }

    public function test_can_get_all_positions()
    {
        Position::factory()->count(3)->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);

        $response = $this->getJson('/api/hr/positions');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_position()
    {
        $data = $this->getValidPositionData();
        $response = $this->postJson('/api/hr/positions', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['position_title' => $data['position_title']]);
        $this->assertDatabaseHas('hr_positions', ['position_title' => $data['position_title']]);
    }

    public function test_create_position_with_reports_to()
    {
        $reportsToPosition = Position::factory()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        $data = $this->getValidPositionData(['reports_to_position_id' => $reportsToPosition->id]);

        $response = $this->postJson('/api/hr/positions', $data);
        $response->assertStatus(201)
                 ->assertJsonFragment(['reports_to_position_id' => $reportsToPosition->id]);
    }

    public function test_create_position_fails_with_invalid_job_id()
    {
        $data = $this->getValidPositionData(['hr_job_id' => 999]); // Non-existent job
        $response = $this->postJson('/api/hr/positions', $data);
        $response->assertStatus(422)->assertJsonValidationErrors(['hr_job_id']);
    }

    public function test_can_get_a_position()
    {
        $position = Position::factory()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        $response = $this->getJson("/api/hr/positions/{$position->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['position_title' => $position->position_title]);
    }

    public function test_can_update_position()
    {
        $position = Position::factory()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        $updatedData = [
            'position_title' => 'Updated Position - ' . $this->faker->word,
            'description' => 'Updated description for position.',
            'is_vacant' => false,
        ];

        $response = $this->putJson("/api/hr/positions/{$position->id}", $updatedData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['position_title' => $updatedData['position_title']]);
        $this->assertDatabaseHas('hr_positions', ['id' => $position->id, 'position_title' => $updatedData['position_title']]);
    }

    public function test_update_position_fails_if_reporting_to_self()
    {
        $position = Position::factory()->create();
        $response = $this->putJson("/api/hr/positions/{$position->id}", [
            'position_title' => $position->position_title, // Required field
            'reports_to_position_id' => $position->id
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['reports_to_position_id']);
    }

    public function test_update_position_fails_if_creating_reporting_cycle()
    {
        $posA = Position::factory()->create(['hr_job_id' => $this->job->id, 'hr_department_id' => $this->department->id]);
        $posB = Position::factory()->create(['hr_job_id' => $this->job->id, 'hr_department_id' => $this->department->id, 'reports_to_position_id' => $posA->id]);
        $posC = Position::factory()->create(['hr_job_id' => $this->job->id, 'hr_department_id' => $this->department->id, 'reports_to_position_id' => $posB->id]);

        // Try to make PosA report to PosC (A -> B -> C ... and A -> C would be A -> B -> C -> A)
        $response = $this->putJson("/api/hr/positions/{$posA->id}", [
            'position_title' => $posA->position_title, // Required field
            'reports_to_position_id' => $posC->id
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Cannot set "reports to" to one of its own descendants.']);
    }


    public function test_can_delete_vacant_position_with_no_reports()
    {
        $position = Position::factory()->vacant()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        $response = $this->deleteJson("/api/hr/positions/{$position->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('hr_positions', ['id' => $position->id]);
    }

    public function test_cannot_delete_filled_position()
    {
        $position = Position::factory()->filled()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        // Create an employee for this position
        Employee::factory()->create([
            'hr_position_id' => $position->id,
            'hr_department_id' => $position->department->id, // ensure consistency
        ]);

        // Refresh position to get employee relationship
        $position->refresh();

        $response = $this->deleteJson("/api/hr/positions/{$position->id}");
        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Cannot delete a filled position. Vacate the position or reassign the employee first.']);
    }

    public function test_cannot_delete_position_with_direct_reports()
    {
        $managerPosition = Position::factory()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
        ]);
        Position::factory()->create([
            'hr_job_id' => $this->job->id,
            'hr_department_id' => $this->department->id,
            'reports_to_position_id' => $managerPosition->id
        ]);

        $response = $this->deleteJson("/api/hr/positions/{$managerPosition->id}");
        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Cannot delete position with direct reports. Reassign reporting structure first.']);
    }
}
