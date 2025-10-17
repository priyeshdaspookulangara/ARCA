<?php

namespace Modules\CRM\Loyalty\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class LoyaltyPoints extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'loyalty_program_id',
        'points',
        'transaction_id',
        'transaction_type',
        'expiry_date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        // return \Modules\CRM\Database\factories\LoyaltyPointsFactory::new();
    }
}