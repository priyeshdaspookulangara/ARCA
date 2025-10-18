<?php

namespace Modules\Analytics\Dimensions\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DimStore extends Model
{
    use HasFactory;

    protected $table = 'dim_stores';

    protected $fillable = [
        'store_id',
        'location',
        'region',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\DimStoreFactory::new();
    }
}