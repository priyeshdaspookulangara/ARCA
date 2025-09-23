<?php

namespace Modules\Fina\PC\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class CostObjectControlling extends Model
{
    protected $table = 'fina_pc_cost_object_controlling';

    protected $fillable = [
        'cost_object',
        'cost_object_type',
        'planned_costs',
        'actual_costs',
        'variance',
        'currency',
    ];
}
