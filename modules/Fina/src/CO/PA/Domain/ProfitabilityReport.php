<?php

namespace Modules\Fina\CO\PA\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Fina\Database\Factories\ProfitabilityReportFactory;

class ProfitabilityReport extends Model
{
    use HasFactory;
    protected $table = 'fina_co_pa_profitability_reports';

    protected static function newFactory()
    {
        return ProfitabilityReportFactory::new();
    }

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