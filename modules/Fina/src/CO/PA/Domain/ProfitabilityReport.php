<?php

namespace Modules\Fina\CO\PA\Domain;

use Illuminate\Database\Eloquent\Model;

class ProfitabilityReport extends Model
{
    protected $table = 'fina_co_pa_profitability_reports';

    protected $fillable = [
        'market_segment_id',
        'revenue',
        'cost',
        'profit',
        'period',
    ];

    public function marketSegment()
    {
        return $this->belongsTo(MarketSegment::class);
    }
}