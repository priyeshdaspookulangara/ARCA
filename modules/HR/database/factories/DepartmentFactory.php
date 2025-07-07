<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee; // If you want to assign a manager

class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company . ' Department',
            'description' => $this->faker->optional()->sentence,
            'parent_department_id' => null, // By default, no parent
            // 'manager_id' => null, // By default, no manager. Can be set explicitly or via a state.
            // Example for setting a manager if EmployeeFactory exists and is usable:
            // 'manager_id' => Employee::factory(),
        ];
    }

    /**
     * Indicate that the department has a parent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withParent(Department $parent = null)
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_department_id' => $parent ? $parent->id : Department::factory(),
            ];
        });
    }

    /**
     * Indicate that the department has a manager.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withManager(Employee $manager = null)
    {
        // This assumes EmployeeFactory is set up and can be called like this.
        // If Employee creation is complex (e.g., needs position, which needs job/dept),
        // this might need to be simpler or manager_id set manually in tests for now.
        return $this->state(function (array $attributes) use ($manager) {
            return [
                // 'manager_id' => $manager ? $manager->id : Employee::factory(),
                 'manager_id' => null, // Placeholder: Manager creation can be complex
            ];
        });
    }
}
