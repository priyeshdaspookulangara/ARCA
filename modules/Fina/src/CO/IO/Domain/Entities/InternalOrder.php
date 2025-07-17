<?php

namespace Modules\Fina\CO\IO\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class InternalOrder extends Model
{
    protected $table = 'fina_co_internal_order_master';

    protected $fillable = [
        'controlling_area_id',
        'order_number',
        'description',
        'order_type_id',
        'responsible_cost_center_id',
        'status',
    ];
}
