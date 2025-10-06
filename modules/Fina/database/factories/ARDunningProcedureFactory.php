<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AR\Domain\Entities\ARDunningProcedure;

class ARDunningProcedureFactory extends Factory
{
    protected $model = ARDunningProcedure::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'dunning_levels' => [
                ['level' => 1, 'days_in_arrears' => 15, 'charge' => 5.00, 'grace_period_days' => 7],
                ['level' => 2, 'days_in_arrears' => 30, 'charge' => 15.00, 'grace_period_days' => 7],
                ['level' => 3, 'days_in_arrears' => 60, 'charge' => 50.00, 'grace_period_days' => 7],
            ],
        ];
    }
}