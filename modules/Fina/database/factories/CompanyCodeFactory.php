<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\GL\Domain\Entities\CompanyCode;
use Modules\Fina\FI\GL\Domain\Entities\ChartOfAccount;
use Modules\Fina\FI\GL\Domain\Entities\FiscalYearVariant;

class CompanyCodeFactory extends Factory
{
    protected $model = CompanyCode::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->numerify('####'),
            'name' => $this->faker->company,
            'country_code' => $this->faker->countryCode,
            'local_currency_code' => $this->faker->currencyCode,
            'chart_of_accounts_id' => ChartOfAccount::factory(),
            'fiscal_year_variant_id' => FiscalYearVariant::factory(),
            'default_tax_country_code' => $this->faker->countryCode,
        ];
    }
}