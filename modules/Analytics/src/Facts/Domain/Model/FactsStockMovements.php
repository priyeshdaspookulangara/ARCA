<?php

namespace Modules\Analytics\Facts\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FactsStockMovements extends Model
{
    use HasFactory;

    protected $table = 'facts_stock_movements';

    protected $fillable = [
        'movement_id',
        'date_id',
        'store_id',
        'product_id',
        'qty',
        'movement_type',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\FactsStockMovementsFactory::new();
    }
}