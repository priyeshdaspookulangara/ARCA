<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ARCustomerFinancials extends Model
{
    protected $table = 'fina_ar_customer_financials';

    protected $fillable = [
        'customer_id',
        'company_code_id',
        'reconciliation_gl_account_id',
        'payment_terms_id',
        'credit_limit',
        'dunning_procedure_id',
        'last_dunned_on',
        'dunning_level',
    ];

    public function dunningProcedure()
    {
        return $this->belongsTo(ARDunningProcedure::class, 'dunning_procedure_id');
    }
}
