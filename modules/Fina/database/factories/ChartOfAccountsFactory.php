<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccounts;

class ChartOfAccountsFactory extends Factory
{
    protected $model = ChartOfAccounts::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
        ];
    }
}