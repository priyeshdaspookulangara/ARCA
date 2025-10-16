<?php

namespace Modules\SD\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'sd_invoices';

    protected $fillable = [
        'sales_order_id',
        'status',
        'total_amount',
        'invoice_date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}