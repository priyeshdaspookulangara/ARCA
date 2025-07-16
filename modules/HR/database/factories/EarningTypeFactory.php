<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Payroll\Domain\Entities\EarningType;

class EarningTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EarningType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word . ' Earning',
            'is_taxable' => $this->faker->boolean,
            'description' => $this->faker->optional()->sentence,
            'is_active' => true,
        ];
    }
}
