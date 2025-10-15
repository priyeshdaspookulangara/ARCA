<?php

namespace Modules\Fina\CO\PA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketSegment extends Model
{
    use HasFactory;

    protected $table = 'fina_co_pa_market_segments';

    protected $fillable = [
        'name',
        'description',
        'characteristics',
    ];

    protected $casts = [
        'characteristics' => 'array',
    ];

    public function profitabilityReports()
    {
        return $this->hasMany(ProfitabilityReport::class);
    }

    protected static function newFactory()
    {
        return \Modules\Fina\Database\factories\CO\PA\MarketSegmentFactory::new();
    }
}