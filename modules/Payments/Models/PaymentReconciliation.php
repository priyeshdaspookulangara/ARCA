<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    protected $fillable = [
        'file_name',
        'matched_count',
        'unmatched_count',
        'remarks',
    ];
}
