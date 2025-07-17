<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class APPaymentInvoiceLink extends Model
{
    protected $table = 'fina_ap_payment_invoice_links';

    protected $fillable = [
        'payment_header_id',
        'invoice_header_id',
        'cleared_amount',
    ];
}
