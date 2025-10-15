<?php

namespace Modules\Fina\CO\PA\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Fina\Database\Factories\MarketSegmentFactory;

class MarketSegment extends Model
{
    use HasFactory;
    protected $table = 'fina_co_pa_market_segments';

    protected static function newFactory()
    {
        return MarketSegmentFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
    ];
}