<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ExchangeRateType extends Model
{
    protected $table = 'fina_exchange_rate_types';

    protected $fillable = [
        'code',
        'name',
    ];
}
