<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettlement extends Model
{
    protected $fillable = [
        'date',
        'total_amount',
        'gateway_id',
        'status',
    ];
}
