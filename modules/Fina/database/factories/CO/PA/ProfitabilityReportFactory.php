<?php

namespace Modules\Fina\Database\Factories\CO\PA;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PA\Domain\Entities\MarketSegment;
use Modules\Fina\CO\PA\Domain\Entities\ProfitabilityReport;

class ProfitabilityReportFactory extends Factory
{
    protected $model = ProfitabilityReport::class;

    public function definition()
    {
        $revenue = $this->faker->randomFloat(2, 10000, 100000);
        $costOfSales = $this->faker->randomFloat(2, 5000, 50000);
        $grossProfit = $revenue - $costOfSales;
        $detailedCosts = [
            ['name' => 'Marketing', 'amount' => $this->faker->randomFloat(2, 1000, 10000)],
            ['name' => 'Administrative', 'amount' => $this->faker->randomFloat(2, 2000, 20000)],
        ];
        $netProfit = $grossProfit - $detailedCosts[0]['amount'] - $detailedCosts[1]['amount'];

        return [
            'market_segment_id' => MarketSegment::factory(),
            'period_start_date' => $this->faker->date(),
            'period_end_date' => $this->faker->date(),
            'revenue' => $revenue,
            'cost_of_sales' => $costOfSales,
            'gross_profit' => $grossProfit,
            'detailed_costs' => $detailedCosts,
            'net_profit' => $netProfit,
        ];
    }
}