<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PC\Domain\ActivityType;

class ActivityTypeFactory extends Factory
{
    protected $model = ActivityType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'unit' => $this->faker->randomElement(['hours', 'pieces', 'kg']),
            'description' => $this->faker->sentence,
        ];
    }
}