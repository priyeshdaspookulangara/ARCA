<?php

namespace Modules\Fina\FI\AR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ARDunningProcedure extends Model
{
    protected $table = 'fina_ar_dunning_procedures';

    protected $fillable = [
        'code',
        'description',
        'dunning_levels',
    ];

    protected $casts = [
        'dunning_levels' => 'json',
    ];
}
