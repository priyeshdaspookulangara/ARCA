<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\FiscalYearVariant;

class FiscalYearVariantFactory extends Factory
{
    protected $model = FiscalYearVariant::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word,
            'name' => $this->faker->sentence,
            'number_of_posting_periods' => 12,
            'number_of_special_periods' => 4,
            'is_year_dependent' => false,
        ];
    }
}