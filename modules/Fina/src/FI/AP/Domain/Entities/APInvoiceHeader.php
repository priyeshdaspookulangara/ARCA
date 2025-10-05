<?php

namespace Modules\Fina\FI\AP\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class APInvoiceHeader extends Model
{
    protected $table = 'fina_ap_invoices_header';

    protected $fillable = [
        'gl_document_header_id',
        'vendor_id',
        'invoice_number_vendor',
        'invoice_date',
        'due_date',
        'gross_amount',
        'net_amount',
        'tax_amount',
        'payment_status',
        'payment_block',
        'payment_run_id',
        'po_number',
    ];
}
