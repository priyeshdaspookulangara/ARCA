<?php

namespace Modules\Analytics\Dimensions\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DimProduct extends Model
{
    use HasFactory;

    protected $table = 'dim_products';

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'category',
        'cost',
        'price',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\DimProductFactory::new();
    }
}