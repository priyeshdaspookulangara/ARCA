<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\TR\Domain\BankBalance;
use Modules\Fina\FI\BL\Domain\Entities\BankAccount;

class BankBalanceFactory extends Factory
{
    protected $model = BankBalance::class;

    public function definition()
    {
        return [
            'bank_account_id' => BankAccount::factory(),
            'balance_date' => $this->faker->date(),
            'balance' => $this->faker->randomFloat(2, 1000, 1000000),
        ];
    }
}