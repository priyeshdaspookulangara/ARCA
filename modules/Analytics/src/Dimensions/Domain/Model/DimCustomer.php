<?php

namespace Modules\Analytics\Dimensions\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DimCustomer extends Model
{
    use HasFactory;

    protected $table = 'dim_customers';

    protected $fillable = [
        'customer_id',
        'name',
        'signup_date',
        'segment',
        'lifetime_value',
    ];

    protected $casts = [
        'signup_date' => 'date',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\Analytics\Database\factories\DimCustomerFactory::new();
    }
}