<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PA\Domain\MarketSegment;
use Modules\Fina\CO\PA\Domain\ProfitabilityReport;

class ProfitabilityReportFactory extends Factory
{
    protected $model = ProfitabilityReport::class;

    public function definition()
    {
        return [
            'market_segment_id' => MarketSegment::factory(),
            'revenue' => $this->faker->randomFloat(2, 1000, 100000),
            'cost' => $this->faker->randomFloat(2, 500, 50000),
            'profit' => $this->faker->randomFloat(2, 500, 50000),
            'period' => $this->faker->date(),
        ];
    }
}