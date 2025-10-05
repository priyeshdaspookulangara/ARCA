<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\BL\Domain\Entities\BankAccount;
use Modules\Fina\FI\BL\Domain\Entities\BankMaster;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition()
    {
        return [
            'account_number' => $this->faker->iban(),
            'account_holder' => $this->faker->name,
            'currency' => $this->faker->currencyCode,
            'iban' => $this->faker->iban(),
            'bank_id' => BankMaster::factory(),
        ];
    }
}