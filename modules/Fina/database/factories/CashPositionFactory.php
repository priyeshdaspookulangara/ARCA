<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\TR\Domain\CashPosition;

class CashPositionFactory extends Factory
{
    protected $model = CashPosition::class;

    public function definition()
    {
        return [
            'position_date' => $this->faker->date(),
            'currency' => $this->faker->currencyCode,
            'amount' => $this->faker->randomFloat(2, 10000, 1000000),
            'description' => $this->faker->sentence,
        ];
    }
}