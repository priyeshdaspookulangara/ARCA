<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AR\Domain\Entities\DunningHistory;
use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;

class DunningHistoryFactory extends Factory
{
    protected $model = DunningHistory::class;

    public function definition()
    {
        return [
            'customer_financials_id' => ARCustomerFinancials::factory(),
            'dunning_date' => $this->faker->date(),
            'dunning_level' => $this->faker->numberBetween(1, 3),
            'dunning_notice_content' => $this->faker->paragraph,
        ];
    }
}