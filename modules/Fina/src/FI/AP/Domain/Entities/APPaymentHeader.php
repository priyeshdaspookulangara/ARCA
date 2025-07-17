<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class APPaymentHeader extends Model
{
    protected $table = 'fina_ap_payments_header';

    protected $fillable = [
        'gl_document_header_id',
        'payment_run_id',
        'payment_method_used',
        'house_bank_account_id',
    ];
}
