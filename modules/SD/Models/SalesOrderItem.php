<?php

namespace Modules\SD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $table = 'sd_sales_order_items';

    protected $fillable = [
        'sales_order_id',
        'material_id',
        'quantity',
        'price',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}