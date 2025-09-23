<?php

namespace Modules\Fina\PC\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class MaterialCost extends Model
{
    protected $table = 'fina_pc_material_costs';

    protected $fillable = [
        'material_id',
        'costing_variant',
        'cost',
        'currency',
    ];
}
