<?php

namespace Modules\Fina\TR\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Fina\FI\BL\Domain\Entities\BankAccount;

class BankBalance extends Model
{
    use HasFactory;

    protected $table = 'fina_tr_bank_balances';

    protected $fillable = [
        'bank_account_id',
        'balance_date',
        'balance',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}