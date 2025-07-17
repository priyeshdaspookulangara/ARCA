<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ARInvoiceHeader extends Model
{
    protected $table = 'fina_ar_invoices_header';

    protected $fillable = [
        'gl_document_header_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'gross_amount',
        'net_amount',
        'tax_amount',
        'payment_status',
        'so_number',
    ];
}
