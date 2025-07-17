<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ARReceiptHeader extends Model
{
    protected $table = 'fina_ar_receipts_header';

    protected $fillable = [
        'gl_document_header_id',
        'payment_method_used',
        'house_bank_account_id',
    ];
}
