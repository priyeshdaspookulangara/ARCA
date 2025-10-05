<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PC\Domain\ProductCostHeader;

class ProductCostHeaderFactory extends Factory
{
    protected $model = ProductCostHeader::class;

    public function definition()
    {
        return [
            'product_id' => $this->faker->uuid,
            'costing_variant' => $this->faker->word,
            'costing_date' => $this->faker->date(),
            'total_cost' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}