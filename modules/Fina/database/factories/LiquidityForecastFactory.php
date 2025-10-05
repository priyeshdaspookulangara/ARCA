<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\TR\Domain\LiquidityForecast;

class LiquidityForecastFactory extends Factory
{
    protected $model = LiquidityForecast::class;

    public function definition()
    {
        $inflows = $this->faker->randomFloat(2, 1000, 100000);
        $outflows = $this->faker->randomFloat(2, 500, 50000);
        $net_flow = $inflows - $outflows;

        return [
            'forecast_date' => $this->faker->date(),
            'currency' => $this->faker->currencyCode,
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net_flow' => $net_flow,
            'description' => $this->faker->sentence,
        ];
    }
}