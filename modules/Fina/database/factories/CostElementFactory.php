<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PC\Domain\CostElement;

class CostElementFactory extends Factory
{
    protected $model = CostElement::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['primary', 'secondary']),
            'description' => $this->faker->sentence,
        ];
    }
}