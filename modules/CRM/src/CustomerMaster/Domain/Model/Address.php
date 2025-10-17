<?php

namespace Modules\CRM\CustomerMaster\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'street',
        'city',
        'state',
        'zip_code',
        'country',
        'type' // e.g., 'billing', 'shipping'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\AddressFactory::new();
    }
}