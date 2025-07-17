<?php

namespace Modules\Fina\FI\GL\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class GLAccountGroup extends Model
{
    protected $table = 'fina_gl_account_groups';

    protected $fillable = [
        'chart_of_accounts_id',
        'group_code',
        'name',
        'from_account_number',
        'to_account_number',
    ];
}
