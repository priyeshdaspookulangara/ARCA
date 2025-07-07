<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private Department $department;
    private Job $job;
    private Position $vacantPosition;
    private Position $filledPosition;
    private Employee $existingEmployee;


    protected function setUp(): void
    {
        parent::setUp();
        $this->department = Department::factory()->create();
        $this->job = Job::factory()->create();

        $this->vacantPosition = Position::factory()->create([
            'hr_department_id' => $this->department->id,
            'hr_job_id' => $this->job->id,
            'is_vacant' => true,
        ]);

        $this->filledPosition = Position::factory()->create([
            'hr_department_id' => $this->department->id,
            'hr_job_id' => $this->job->id,
            'is_vacant' => false, // Will be filled by existingEmployee
        ]);

        $this->existingEmployee = Employee::factory()->create([
            'hr_position_id' => $this->filledPosition->id,
            'hr_department_id' => $this->department->id,
        ]);
        // Ensure the filledPosition is marked as not vacant
        $this->filledPosition->is_vacant = false;
        $this->filledPosition->save();

    }

    private function getValidEmployeeData(array $overrides = []): array
    {
        return array_merge([
            'employee_id_number' => $this->faker->unique()->numerify('EMP-#####'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'work_email' => $this->faker->unique()->safeEmail,
            'hire_date' => now()->subYear()->toDateString(),
            'employment_status' => 'active',
            'hr_position_id' => $this->vacantPosition->id,
            'hr_department_id' => $this->department->id, // Should match position's department
        ], $overrides);
    }

    public function test_can_get_all_employees()
    {
        // We have one existingEmployee from setup, create 2 more
        Employee::factory()->count(2)->create([
             'hr_department_id' => $this->department->id, // ensure they have a department for consistency
        ]);

        $response = $this->getJson('/api/hr/employees');

        $response->assertStatus(200)
                 ->assertJsonCount(3); // existingEmployee + 2 new ones
    }

    public function test_can_create_employee_and_assign_to_vacant_position()
    {
        $data = $this->getValidEmployeeData(['hr_position_id' => $this->vacantPosition->id]);

        $response = $this->postJson('/api/hr/employees', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['employee_id_number' => $data['employee_id_number']]);

        $this->assertDatabaseHas('hr_employees', ['employee_id_number' => $data['employee_id_number']]);
        $this->assertDatabaseHas('hr_positions', ['id' => $this->vacantPosition->id, 'is_vacant' => false]);
    }

    public function test_create_employee_fails_if_assigned_position_is_already_filled()
    {
        // Attempt to assign to the already filledPosition
        $data = $this->getValidEmployeeData(['hr_position_id' => $this->filledPosition->id]);

        $response = $this->postJson('/api/hr/employees', $data);

        // The controller validation for this is a bit basic ("position is currently not vacant")
        // A more robust system might allow assigning if the current occupant is being moved/terminated simultaneously.
        // For now, expecting failure based on the simple vacancy check in store method's validator.
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['hr_position_id']);
    }

    public function test_create_employee_fails_if_department_does_not_match_position_department()
    {
        $otherDepartment = Department::factory()->create();
        $data = $this->getValidEmployeeData([
            'hr_position_id' => $this->vacantPosition->id,
            'hr_department_id' => $otherDepartment->id, // Mismatched department
        ]);

        $response = $this->postJson('/api/hr/employees', $data);
        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'The provided department ID does not match the department of the selected position.']);
    }

    public function test_create_employee_auto_assigns_department_from_position_if_department_not_given()
    {
        $data = $this->getValidEmployeeData([
            'hr_position_id' => $this->vacantPosition->id,
            'hr_department_id' => null, // Department not provided
        ]);
        unset($data['hr_department_id']); // Ensure it's not in the payload

        $response = $this->postJson('/api/hr/employees', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('hr_department_id', $this->vacantPosition->hr_department_id);
        $this->assertDatabaseHas('hr_employees', [
            'employee_id_number' => $data['employee_id_number'],
            'hr_department_id' => $this->vacantPosition->hr_department_id
        ]);
    }


    public function test_can_get_an_employee()
    {
        $response = $this->getJson("/api/hr/employees/{$this->existingEmployee->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['employee_id_number' => $this->existingEmployee->employee_id_number]);
    }

    public function test_can_update_employee_details()
    {
        $newEmail = $this->faker->unique()->safeEmail;
        $updatedData = [
            'work_email' => $newEmail,
            'last_name' => 'Smithson',
        ];

        $response = $this->putJson("/api/hr/employees/{$this->existingEmployee->id}", $updatedData);
        $response->assertStatus(200)
                 ->assertJsonFragment(['work_email' => $newEmail]);
        $this->assertDatabaseHas('hr_employees', ['id' => $this->existingEmployee->id, 'work_email' => $newEmail]);
    }

    public function test_update_employee_can_change_position_to_vacant_one()
    {
        $newVacantPosition = Position::factory()->create([
            'hr_department_id' => $this->department->id,
            'hr_job_id' => $this->job->id,
            'is_vacant' => true,
        ]);

        $response = $this->putJson("/api/hr/employees/{$this->existingEmployee->id}", [
            'hr_position_id' => $newVacantPosition->id,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['hr_position_id' => $newVacantPosition->id]);

        $this->assertDatabaseHas('hr_employees', ['id' => $this->existingEmployee->id, 'hr_position_id' => $newVacantPosition->id]);
        $this->assertDatabaseHas('hr_positions', ['id' => $this->filledPosition->id, 'is_vacant' => true]); // Old position is now vacant
        $this->assertDatabaseHas('hr_positions', ['id' => $newVacantPosition->id, 'is_vacant' => false]); // New position is now filled
    }

    public function test_update_employee_fails_if_changing_to_already_filled_position()
    {
        $anotherEmployee = Employee::factory()->create(); // This employee will fill another position
        $anotherFilledPosition = Position::factory()->create([
            'hr_department_id' => $this->department->id,
            'hr_job_id' => $this->job->id,
            'is_vacant' => false, // Filled by anotherEmployee
        ]);
        $anotherEmployee->hr_position_id = $anotherFilledPosition->id;
        $anotherEmployee->save();


        $response = $this->putJson("/api/hr/employees/{$this->existingEmployee->id}", [
            'hr_position_id' => $anotherFilledPosition->id,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['hr_position_id']);
    }


    public function test_can_delete_employee_and_position_becomes_vacant()
    {
        $employeeToTerminate = Employee::factory()->create();
        $positionId = $employeeToTerminate->hr_position_id;

        // Ensure position exists and is marked as filled by this employee
        if ($positionId) {
            Position::find($positionId)->update(['is_vacant' => false]);
        } else {
            // If employee didn't have a position, assign one for the test
            $tempPosition = Position::factory()->create(['is_vacant' => false, 'hr_department_id' => $this->department->id, 'hr_job_id' => $this->job->id]);
            $employeeToTerminate->hr_position_id = $tempPosition->id;
            $employeeToTerminate->save();
            $positionId = $tempPosition->id;
        }


        $response = $this->deleteJson("/api/hr/employees/{$employeeToTerminate->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted('hr_employees', ['id' => $employeeToTerminate->id]);
        $this->assertDatabaseHas('hr_employees', ['id' => $employeeToTerminate->id, 'employment_status' => 'terminated']);

        if ($positionId) {
            $this->assertDatabaseHas('hr_positions', ['id' => $positionId, 'is_vacant' => true]);
        }
    }
}
