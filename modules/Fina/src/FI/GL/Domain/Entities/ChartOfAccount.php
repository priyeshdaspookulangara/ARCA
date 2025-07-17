<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'fina_charts_of_accounts';

    protected $fillable = [
        'code',
        'name',
        'language_key',
        'length_gl_account_number',
    ];
}
