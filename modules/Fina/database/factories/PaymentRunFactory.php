<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AP\Domain\Entities\PaymentRun;

class PaymentRunFactory extends Factory
{
    protected $model = PaymentRun::class;

    public function definition()
    {
        return [
            'run_date' => $this->faker->date(),
            'status' => 'Proposal Created',
            'parameters' => [
                'run_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
            ],
        ];
    }
}