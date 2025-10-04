<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\CO\PCA\Domain\PcaPosting;
use Modules\Fina\CO\PCA\Domain\ProfitCenter;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;

class PcaPostingFactory extends Factory
{
    protected $model = PcaPosting::class;

    public function definition()
    {
        return [
            'profit_center_id' => ProfitCenter::factory(),
            'gl_account_id' => GLAccount::factory(),
            'document_number' => $this->faker->unique()->numerify('DOC-#####'),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'posting_date' => $this->faker->date(),
            'description' => $this->faker->sentence,
        ];
    }
}