<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class GLAccount extends Model
{
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
}
