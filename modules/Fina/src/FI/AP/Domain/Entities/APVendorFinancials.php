<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class APVendorFinancials extends Model
{
    protected $table = 'fina_ap_vendor_financials';

    protected $fillable = [
        'vendor_id',
        'company_code_id',
        'reconciliation_gl_account_id',
        'payment_terms_id',
        'payment_methods',
        'dunning_procedure_id',
    ];

    protected $casts = [
        'payment_methods' => 'json',
    ];
}
