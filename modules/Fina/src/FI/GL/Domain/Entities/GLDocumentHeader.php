<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class GLDocumentHeader extends Model
{
    protected $table = 'fina_gl_document_headers';

    protected $fillable = [
        'company_code_id',
        'document_number',
        'fiscal_year',
        'document_type',
        'document_date',
        'posting_date',
        'reference_text',
        'header_text',
        'transaction_currency_code',
        'created_by_user_id',
    ];

    public function items()
    {
        return $this->hasMany(GLDocumentItem::class, 'document_header_id');
    }
}
