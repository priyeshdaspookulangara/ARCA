<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\CCA\Domain\Entities\ControllingArea;

class ControllingAreaFactory extends Factory
{
    protected $model = ControllingArea::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' Controlling Area',
            'currency' => $this->faker->currencyCode,
        ];
    }
}