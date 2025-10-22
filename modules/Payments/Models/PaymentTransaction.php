<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_ref',
        'amount',
        'currency',
        'status',
        'gateway_ref',
        'initiated_by',
        'source_module',
    ];
}
