<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Modules\Fina\CO\CCA\Domain\Entities\ControllingArea;

class ProfitCenterFactory extends Factory
{
    protected $model = ProfitCenter::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'controlling_area_id' => ControllingArea::factory(),
            'responsible_person' => $this->faker->name,
        ];
    }
}