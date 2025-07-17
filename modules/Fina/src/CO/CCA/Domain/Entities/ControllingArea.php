<?php

namespace Modules\Fina\CO\CCA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class ControllingArea extends Model
{
    protected $table = 'fina_co_controlling_areas';

    protected $fillable = [
        'code',
        'name',
        'currency_code',
    ];
}
