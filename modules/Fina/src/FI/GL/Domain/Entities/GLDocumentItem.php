<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class GLDocumentItem extends Model
{
    protected $table = 'fina_gl_document_items';

    protected $fillable = [
        'document_header_id',
        'item_number',
        'gl_account_id',
        'posting_type',
        'amount_transaction_currency',
        'amount_local_currency',
        'tax_code_id',
        'tax_amount_local_currency',
        'cost_center_id',
        'internal_order_id',
        'profit_center_id',
        'assignment_text',
        'item_text',
    ];

    public function header()
    {
        return $this->belongsTo(GLDocumentHeader::class, 'document_header_id');
    }
}
