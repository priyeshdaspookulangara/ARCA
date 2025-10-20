<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'tax',
        'last_updated',
    ];

    protected $table = 'product_cache';
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function newFactory()
    {
        return \Modules\POS\Database\Factories\ProductCacheFactory::new();
    }
}
