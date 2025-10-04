<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;

class GLAccountFactory extends Factory
{
    protected $model = GLAccount::class;

    public function definition()
    {
        return [
            'account_number' => $this->faker->unique()->numerify('##########'),
            'description' => $this->faker->sentence,
            'account_type' => $this->faker->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'chart_of_accounts_id' => 1, // Assuming a chart of accounts with ID 1 exists
        ];
    }
}