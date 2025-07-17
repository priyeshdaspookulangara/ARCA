<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class FiscalYearVariant extends Model
{
    protected $table = 'fina_fiscal_year_variants';

    protected $fillable = [
        'code',
        'name',
        'number_of_posting_periods',
        'number_of_special_periods',
        'is_year_dependent',
    ];
}
