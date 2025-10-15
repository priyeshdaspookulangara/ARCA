<?php

namespace Modules\Fina\Database\Factories\CO\PA;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PA\Domain\Entities\MarketSegment;

class MarketSegmentFactory extends Factory
{
    protected $model = MarketSegment::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'characteristics' => [
                'customer_group' => $this->faker->word,
                'product_group' => $this->faker->word,
                'region' => $this->faker->country,
            ],
        ];
    }
}