<?php

namespace Modules\Fina\FI\AA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetValue extends Model
{
    protected $table = 'fina_aa_asset_values';

    protected $fillable = [
        'asset_master_id',
        'depreciation_area_id',
        'fiscal_year',
        'acquisition_cost',
        'accumulated_depreciation',
        'planned_depreciation_for_year',
        'net_book_value',
    ];
}
