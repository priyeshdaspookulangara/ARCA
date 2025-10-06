<?php

namespace Modules\Fina\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fina\FI\AR\Domain\Entities\ARCustomerFinancials;
use Modules\Fina\FI\AR\Domain\Entities\ARDunningProcedure;
use Modules\Fina\Core\Entities\CompanyCode;
use Modules\Fina\FI\GL\Domain\Entities\GLAccount;
use Modules\Fina\FI\AP\Domain\Entities\PaymentTerm;

class ARCustomerFinancialsFactory extends Factory
{
    protected $model = ARCustomerFinancials::class;

    public function definition()
    {
        return [
            'customer_id' => 1, // Assuming a core customer with ID 1 exists
            'company_code_id' => CompanyCode::factory(),
            'reconciliation_gl_account_id' => GLAccount::factory(),
            'payment_terms_id' => PaymentTerm::factory(),
            'credit_limit' => $this->faker->randomFloat(2, 1000, 100000),
            'dunning_procedure_id' => ARDunningProcedure::factory(),
            'last_dunned_on' => null,
            'dunning_level' => 0,
        ];
    }
}