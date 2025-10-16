<?php

namespace Modules\SD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'sd_deliveries';

    protected $fillable = [
        'sales_order_id',
        'status',
        'delivery_date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}