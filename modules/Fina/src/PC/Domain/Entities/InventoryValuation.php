<?php

namespace Modules\Fina\PC\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class InventoryValuation extends Model
{
    protected $table = 'fina_pc_inventory_valuations';

    protected $fillable = [
        'material_id',
        'plant_id',
        'quantity',
        'value',
        'currency',
    ];
}
