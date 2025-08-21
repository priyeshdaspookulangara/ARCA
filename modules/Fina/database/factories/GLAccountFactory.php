<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;

class GLAccountFactory extends Factory
{
    protected $model = GLAccount::class;

    public function definition()
    {
        return [
            'chart_of_accounts_id' => ChartOfAccount::factory(),
            'account_number' => $this->faker->unique()->numerify('##########'),
            'name' => $this->faker->bs,
            'account_type' => $this->faker->randomElement(['Balance Sheet', 'P&L']),
            'is_open_item_managed' => $this->faker->boolean,
        ];
    }
}
