<?php

namespace Modules\POS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\POS\Models\ProductCache;

class ProductCacheFactory extends Factory
{
    protected $model = ProductCache::class;

    public function definition()
    {
        return [
            'product_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'tax' => $this->faker->randomFloat(2, 0, 20),
            'last_updated' => now(),
        ];
    }
}
