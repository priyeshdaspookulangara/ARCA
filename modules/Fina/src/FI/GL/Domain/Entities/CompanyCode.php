<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class CompanyCode extends Model
{
    protected $table = 'fina_company_codes';

    protected $fillable = [
        'code',
        'name',
        'country_code',
        'local_currency_code',
        'chart_of_accounts_id',
        'fiscal_year_variant_id',
        'default_tax_country_code',
    ];
}
