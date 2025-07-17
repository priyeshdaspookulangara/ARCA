<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'fina_exchange_rates';

    protected $fillable = [
        'rate_type_id',
        'from_currency_code',
        'to_currency_code',
        'valid_from_date',
        'exchange_rate',
        'ratio_from',
        'ratio_to',
    ];
}
