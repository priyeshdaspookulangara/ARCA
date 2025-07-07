<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee; // If needed for manager
// Assuming a base TestCase exists in Modules/HR/Tests or project root tests folder
// For now, let's assume it uses Laravel's base TestCase
use Tests\TestCase; // Adjust if your base test case is different

class DepartmentApiTest extends TestCase
{
    use RefreshDatabase; // Resets database for each test
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Ideally, you would run migrations for the HR module and any dependent core modules.
        // Since artisan is problematic, this might be an issue.
        // For now, tests assume tables exist as per migrations.
        // $this->artisan('migrate'); // This would be ideal if artisan worked
    }

    private function createManager(): Employee
    {
        // A simplified manager creation. In a real scenario, an EmployeeFactory would be better.
        // Also, Employee creation might depend on Position, Job, etc. which are not yet fully implemented.
        // This is a placeholder and might need adjustment as Employee CRUD is built.
        return Employee::create([
            'employee_id_number' => $this->faker->unique()->numerify('EMP#####'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'work_email' => $this->faker->unique()->safeEmail,
            'hire_date' => $this->faker->date(),
            // 'hr_position_id' => null, // Position might be needed
            // 'hr_department_id' => null, // Department might be needed
        ]);
    }

    public function test_can_get_all_departments()
    {
        Department::factory()->count(3)->create();

        $response = $this->getJson('/api/hr/departments');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_department()
    {
        $manager = $this->createManager();
        $data = [
            'name' => $this->faker->company . ' Department',
            'description' => $this->faker->sentence,
            'manager_id' => $manager->id,
        ];

        $response = $this->postJson('/api/hr/departments', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('hr_departments', ['name' => $data['name']]);
    }

    public function test_create_department_with_parent()
    {
        $parentDepartment = Department::factory()->create();
        $manager = $this->createManager();
        $data = [
            'name' => 'Sub ' . $this->faker->company,
            'description' => $this->faker->sentence,
            'parent_department_id' => $parentDepartment->id,
            'manager_id' => $manager->id,
        ];

        $response = $this->postJson('/api/hr/departments', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => $data['name'],
                     'parent_department_id' => $parentDepartment->id
                 ]);
        $this->assertDatabaseHas('hr_departments', ['name' => $data['name'], 'parent_department_id' => $parentDepartment->id]);
    }

    public function test_create_department_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/hr/departments', ['name' => '']);
        $response->assertStatus(422) // Validation error
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_create_department_fails_with_non_existent_manager()
    {
        $data = [
            'name' => $this->faker->company . ' Department',
            'manager_id' => 999, // Non-existent manager
        ];
        $response = $this->postJson('/api/hr/departments', $data);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['manager_id']);
    }

    public function test_can_get_a_department()
    {
        $department = Department::factory()->create();
        $response = $this->getJson("/api/hr/departments/{$department->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $department->name]);
    }

    public function test_can_update_department()
    {
        $department = Department::factory()->create();
        $newManager = $this->createManager();
        $updatedData = [
            'name' => 'Updated Department Name',
            'description' => 'Updated description.',
            'manager_id' => $newManager->id,
        ];

        $response = $this->putJson("/api/hr/departments/{$department->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $updatedData['name']]);
        $this->assertDatabaseHas('hr_departments', ['id' => $department->id, 'name' => $updatedData['name']]);
    }

    public function test_update_department_fails_with_invalid_parent_cycle()
    {
        $parent = Department::factory()->create(['name' => 'Parent Dept']);
        $child = Department::factory()->create(['name' => 'Child Dept', 'parent_department_id' => $parent->id]);

        // Try to set parent as child (which is invalid)
        $response = $this->putJson("/api/hr/departments/{$parent->id}", [
            'name' => $parent->name, // Name is required for update in this controller
            'parent_department_id' => $child->id
        ]);
        $response->assertStatus(422); // Should fail due to cycle or specific validation
        // The controller's cycle detection returns:
        // return response()->json(['error' => 'Cannot set parent department to one of its own descendants.'], 422);
        $response->assertJsonFragment(['error' => 'Cannot set parent department to one of its own descendants.']);
    }


    public function test_can_delete_department()
    {
        $department = Department::factory()->create();
        $response = $this->deleteJson("/api/hr/departments/{$department->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('hr_departments', ['id' => $department->id]);
    }

    public function test_cannot_delete_department_with_children()
    {
        $parent = Department::factory()->create();
        Department::factory()->create(['parent_department_id' => $parent->id]);

        $response = $this->deleteJson("/api/hr/departments/{$parent->id}");
        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Cannot delete department with child departments. Reassign children first.']);
    }

    // Placeholder for DepartmentFactory - will need to create this file
    // In a real scenario, factories are essential for testing.
    // For now, tests might partially fail or be incomplete without factories for Department and Employee.
}

// Ensure DepartmentFactory.php is created in modules/HR/database/factories/
// Example:
// <?php
// namespace Modules\HR\Database\Factories;
// use Illuminate\Database\Eloquent\Factories\Factory;
// use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
// class DepartmentFactory extends Factory {
//     protected $model = Department::class;
//     public function definition() {
//         return [
//             'name' => $this->faker->company . ' Department',
//             'description' => $this->faker->sentence,
//             'parent_department_id' => null,
//             'manager_id' => null, // Or use EmployeeFactory
//         ];
//     }
// }
