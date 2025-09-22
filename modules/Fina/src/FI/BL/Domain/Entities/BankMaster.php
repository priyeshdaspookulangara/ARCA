<?php

namespace Modules\Fina\FI\BL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class BankMaster extends Model
{
    protected $table = 'fina_bl_bank_master';

    protected $fillable = [
        'bank_name',
        'bank_key',
        'address',
        'swift_code',
    ];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'bank_id');
    }
}
