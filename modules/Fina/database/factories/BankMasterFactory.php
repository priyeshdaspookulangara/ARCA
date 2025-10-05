<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\BL\Domain\Entities\BankMaster;

class BankMasterFactory extends Factory
{
    protected $model = BankMaster::class;

    public function definition()
    {
        return [
            'bank_name' => $this->faker->company . ' Bank',
            'bank_key' => $this->faker->unique()->numerify('BANK####'),
            'address' => $this->faker->address,
            'swift_code' => $this->faker->swiftBicNumber,
        ];
    }
}