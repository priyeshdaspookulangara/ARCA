<?php

namespace Modules\Fina\CO\PA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitabilityReport extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pa_profitability_reports';

    protected $fillable = [
        'market_segment_id',
        'period_start_date',
        'period_end_date',
        'revenue',
        'cost_of_sales',
        'gross_profit',
        'detailed_costs',
        'net_profit',
    ];

    protected $casts = [
        'detailed_costs' => 'array',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
    ];

    public function marketSegment()
    {
        return $this->belongsTo(MarketSegment::class);
    }

    protected static function newFactory()
    {
        return \Modules\Fina\Database\factories\CO\PA\ProfitabilityReportFactory::new();
    }
}