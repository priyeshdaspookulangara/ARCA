<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Payroll\Domain\Entities\DeductionType;

class DeductionTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeductionType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word . ' Deduction',
            'is_pre_tax' => $this->faker->boolean,
            'description' => $this->faker->optional()->sentence,
            'is_active' => true,
        ];
    }
}
