<?php

namespace Modules\Analytics\Dimensions\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DimDate extends Model
{
    use HasFactory;

    protected $table = 'dim_date';

    protected $fillable = [
        'date_id',
        'date',
        'year',
        'month',
        'week',
        'day_of_week',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\DimDateFactory::new();
    }
}