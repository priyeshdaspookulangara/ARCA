<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Position;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;
use Modules\HR\PersonnelAdmin\Domain\Entities\Department;

class PositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Position::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure Job and Department exist or are created by their factories
        $job = Job::factory()->create();
        $department = Department::factory()->create();

        return [
            'position_title' => $this->faker->jobTitle . ' (' . $department->name . ')',
            'hr_job_id' => $job->id,
            'hr_department_id' => $department->id,
            'description' => $this->faker->optional()->paragraph,
            'reports_to_position_id' => null, // By default, no reporting line
            'is_vacant' => $this->faker->boolean(75), // 75% chance of being vacant
            'effective_date_start' => $this->faker->optional()->date('Y-m-d', '-1 year'),
            'effective_date_end' => null,
        ];
    }

    /**
     * Indicate that the position reports to another position.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function reportsTo(Position $reportsToPosition = null)
    {
        return $this->state(function (array $attributes) use ($reportsToPosition) {
            return [
                'reports_to_position_id' => $reportsToPosition ? $reportsToPosition->id : Position::factory(),
            ];
        });
    }

    /**
     * Indicate that the position is vacant.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function vacant()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_vacant' => true,
            ];
        });
    }

    /**
     * Indicate that the position is filled (not vacant).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function filled()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_vacant' => false,
            ];
        });
    }
}
