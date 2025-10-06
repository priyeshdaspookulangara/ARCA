<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\Core\Entities\CompanyCode;

class CompanyCodeFactory extends Factory
{
    protected $model = CompanyCode::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->numerify('####'),
            'name' => $this->faker->company,
            'currency' => $this->faker->currencyCode,
        ];
    }
}