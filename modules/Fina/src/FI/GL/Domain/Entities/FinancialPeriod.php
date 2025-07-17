<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class FinancialPeriod extends Model
{
    protected $table = 'fina_financial_periods';

    protected $fillable = [
        'fiscal_year_variant_id',
        'year',
        'period',
        'start_date',
        'end_date',
        'is_open_for_posting',
    ];
}
