<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PA\Domain\ProfitabilityReport;
use Modules\Fina\CO\PA\Domain\MarketSegment;

class ProfitabilityReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProfitabilityReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $revenue = $this->faker->randomFloat(2, 1000, 100000);
        $cost = $this->faker->randomFloat(2, 500, 50000);
        return [
            'market_segment_id' => MarketSegment::factory(),
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $revenue - $cost,
            'period' => $this->faker->date(),
        ];
    }
}