<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position; // Required if hr_position_id is not nullable
use Modules\HR\PersonnelAdmin\Domain\Entities\Department; // Required if hr_department_id is not nullable

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Simplified: In a real app, ensure Position and Department exist or are created.
        // For now, we might need to make hr_position_id and hr_department_id nullable in the migration
        // or ensure they are created before EmployeeFactory is called.
        // Let's assume they can be null for basic employee creation for now, or set them up in tests.

        return [
            'employee_id_number' => $this->faker->unique()->numerify('EMP######'),
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional(0.3)->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->optional()->date('Y-m-d', '-20 years'),
            'gender' => $this->faker->optional()->randomElement(['male', 'female', 'other']),
            'nationality' => $this->faker->optional()->country,
            'marital_status' => $this->faker->optional()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'personal_email' => $this->faker->optional()->unique()->safeEmail,
            'work_email' => $this->faker->unique()->safeEmail,
            'phone_mobile' => $this->faker->optional()->phoneNumber,
            'phone_work' => $this->faker->optional()->phoneNumber,
            'address_line_1' => $this->faker->optional()->streetAddress,
            'city' => $this->faker->optional()->city,
            'country' => $this->faker->optional()->country,
            'hire_date' => $this->faker->date('Y-m-d', '-5 years'),
            'termination_date' => null,
            'employment_status' => 'active',
            'employment_type' => $this->faker->optional()->randomElement(['full-time', 'part-time', 'contract']),
            'hr_position_id' => null, // Or Position::factory() if PositionFactory is simple enough
            'hr_department_id' => null, // Or Department::factory()
            'emergency_contact_name' => $this->faker->optional()->name,
            'emergency_contact_relationship' => $this->faker->optional()->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'emergency_contact_phone' => $this->faker->optional()->phoneNumber,
        ];
    }

    /**
     * Indicate that the employee is assigned to a specific position.
     */
    public function forPosition(Position $position)
    {
        return $this->state(function (array $attributes) use ($position) {
            return [
                'hr_position_id' => $position->id,
                'hr_department_id' => $position->hr_department_id, // Auto-assign department from position
            ];
        });
    }

     /**
     * Indicate that the employee is assigned to a specific department.
     * Note: This should ideally be consistent with the position's department.
     */
    public function forDepartment(Department $department)
    {
        return $this->state(function (array $attributes) use ($department) {
            return [
                'hr_department_id' => $department->id,
            ];
        });
    }
}
