<?php

namespace Modules\Fina\FI\BL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $table = 'fina_bl_bank_statements';

    protected $fillable = [
        'statement_date',
        'opening_balance',
        'closing_balance',
        'bank_account_id',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
