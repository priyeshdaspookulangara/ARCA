<?php

namespace Modules\Analytics\Facts\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FactsSales extends Model
{
    use HasFactory;

    protected $table = 'facts_sales';

    protected $fillable = [
        'sale_id',
        'date_id',
        'store_id',
        'customer_id',
        'total',
        'tax',
        'cost',
        'payment_mode',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\FactsSalesFactory::new();
    }
}