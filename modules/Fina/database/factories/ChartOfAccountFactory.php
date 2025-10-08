<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;

class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'name' => $this->faker->sentence,
            'language_key' => 'EN',
            'length_gl_account_number' => 10,
        ];
    }
}