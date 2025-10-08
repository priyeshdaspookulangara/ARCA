<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AP\Domain\Entities\PaymentTerm;

class PaymentTermFactory extends Factory
{
    protected $model = PaymentTerm::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'rules' => json_encode(['days' => $this->faker->randomElement([15, 30, 45, 60])]),
        ];
    }
}