<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PC\Domain\ProductCostHeader;
use Modules\Fina\CO\PC\Domain\CostElement;
use Modules\Fina\CO\PC\Domain\ActivityType;
use Modules\Fina\CO\PC\Domain\ProductCostItem;

class ProductCostItemFactory extends Factory
{
    protected $model = ProductCostItem::class;

    public function definition()
    {
        $quantity = $this->faker->randomFloat(5, 1, 100);
        $rate = $this->faker->randomFloat(5, 10, 500);
        $cost = $quantity * $rate;

        return [
            'product_cost_header_id' => ProductCostHeader::factory(),
            'cost_element_id' => CostElement::factory(),
            'activity_type_id' => ActivityType::factory(),
            'quantity' => $quantity,
            'rate' => $rate,
            'cost' => $cost,
        ];
    }
}