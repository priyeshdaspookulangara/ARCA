<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ARReceiptInvoiceLink extends Model
{
    protected $table = 'fina_ar_receipt_invoice_links';

    protected $fillable = [
        'receipt_header_id',
        'invoice_header_id',
        'cleared_amount',
    ];
}
