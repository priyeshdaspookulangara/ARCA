<?php

namespace Modules\Fina\TR\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashPosition extends Model
{
    use HasFactory;

    protected $table = 'fina_tr_cash_positions';

    protected $fillable = [
        'position_date',
        'currency',
        'amount',
        'description',
    ];
}