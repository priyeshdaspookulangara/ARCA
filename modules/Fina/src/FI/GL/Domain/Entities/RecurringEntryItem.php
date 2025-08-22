<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringEntryItem extends Model
{
    use HasFactory;

    protected $table = 'fina_gl_recurring_entry_items';

    protected $fillable = [
        'recurring_document_id',
        'gl_account_id',
        'posting_type',
        'amount_transaction_currency',
        'item_text',
    ];

    public function recurringDocument()
    {
        return $this->belongsTo(RecurringEntryDocument::class, 'recurring_document_id');
    }

    protected static function newFactory()
    {
        return \Modules\Fina\Database\Factories\RecurringEntryItemFactory::new();
    }
}
