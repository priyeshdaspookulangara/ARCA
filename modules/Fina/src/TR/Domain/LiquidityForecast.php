<?php

namespace Modules\Fina\TR\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiquidityForecast extends Model
{
    use HasFactory;

    protected $table = 'fina_tr_liquidity_forecasts';

    protected $fillable = [
        'forecast_date',
        'currency',
        'inflows',
        'outflows',
        'net_flow',
        'description',
    ];
}