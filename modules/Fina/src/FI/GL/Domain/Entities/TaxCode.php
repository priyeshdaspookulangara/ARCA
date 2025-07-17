<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class TaxCode extends Model
{
    protected $table = 'fina_tax_codes';

    protected $fillable = [
        'country_code',
        'tax_code',
        'description',
        'tax_type',
        'tax_rate_percentage',
        'gl_account_id_for_posting',
    ];
}
