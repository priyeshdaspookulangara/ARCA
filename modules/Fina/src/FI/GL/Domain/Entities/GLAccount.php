<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLAccount extends Model
{
    use HasFactory;

    protected $table = 'fina_gl_accounts';

    protected $fillable = [
        'chart_of_accounts_id',
        'account_number',
        'name',
        'account_type',
        'gl_account_group_id',
        'is_reconciliation_account_for',
        'tax_category_id',
        'is_balance_only_in_local_currency',
        'is_open_item_managed',
        'sort_key',
    ];

    /**
     * Get the chart of accounts that owns the GL account.
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_accounts_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Fina\Database\Factories\GLAccountFactory::new();
    }
}
