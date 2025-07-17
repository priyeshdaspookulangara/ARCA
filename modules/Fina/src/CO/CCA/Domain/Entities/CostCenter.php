<?php

namespace Modules\Fina\CO\CCA\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $table = 'fina_co_cost_centers_master';

    protected $fillable = [
        'controlling_area_id',
        'cost_center_code',
        'name',
        'valid_from_date',
        'valid_to_date',
        'person_responsible_user_id',
        'hierarchy_node_id',
        'company_code_id',
        'profit_center_id',
    ];
}
