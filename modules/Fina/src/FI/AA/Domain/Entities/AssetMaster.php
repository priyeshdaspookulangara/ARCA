<?php

namespace Modules\Fina\FI\AA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class AssetMaster extends Model
{
    protected $table = 'fina_aa_asset_master';

    protected $fillable = [
        'company_code_id',
        'asset_number',
        'asset_subnumber',
        'description',
        'asset_class_id',
        'capitalization_date',
        'cost_center_id',
        'quantity',
        'unit_of_measure',
        'vendor_id',
        'status',
    ];
}
