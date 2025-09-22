<?php

namespace Modules\Fina\FI\BL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table = 'fina_bl_bank_accounts';

    protected $fillable = [
        'account_number',
        'account_holder',
        'currency',
        'iban',
        'bank_id',
    ];

    public function bank()
    {
        return $this->belongsTo(BankMaster::class, 'bank_id');
    }

    public function bankStatements()
    {
        return $this->hasMany(BankStatement::class, 'bank_account_id');
    }
}
