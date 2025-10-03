<?php

namespace Modules\Fina\CO\PA\Domain;

use Illuminate\Database\Eloquent\Model;

class MarketSegment extends Model
{
    protected $table = 'fina_co_pa_market_segments';

    protected $fillable = [
        'name',
        'description',
    ];
}