<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minSalary = $this->faker->optional(0.7)->numberBetween(30000, 80000);
        $maxSalary = null;
        if ($minSalary !== null) {
            $maxSalary = $this->faker->optional(0.8)->numberBetween($minSalary + 10000, $minSalary + 50000);
        }

        return [
            'job_title' => $this->faker->unique()->jobTitle,
            'job_description' => $this->faker->optional()->paragraph,
            'job_code' => $this->faker->optional(0.5)->unique()->bothify('JOB###??'),
            'min_salary' => $minSalary,
            'max_salary' => $maxSalary,
        ];
    }
}
