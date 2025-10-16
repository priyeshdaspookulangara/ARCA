<?php

namespace Modules\SD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'sd_customers';

    protected $fillable = [
        'name',
        'shipping_address',
        'billing_address',
        'credit_limit',
    ];
}