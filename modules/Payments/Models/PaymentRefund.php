<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRefund extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'reason',
        'status',
    ];
}
