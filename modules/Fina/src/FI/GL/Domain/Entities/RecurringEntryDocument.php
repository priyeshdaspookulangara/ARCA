<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringEntryDocument extends Model
{
    use HasFactory;

    protected $table = 'fina_gl_recurring_entry_documents';

    protected $fillable = [
        'company_code_id',
        'document_type',
        'transaction_currency_code',
        'header_text',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'last_run_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(RecurringEntryItem::class, 'recurring_document_id');
    }

    protected static function newFactory()
    {
        return \Modules\Fina\Database\Factories\RecurringEntryDocumentFactory::new();
    }
}
