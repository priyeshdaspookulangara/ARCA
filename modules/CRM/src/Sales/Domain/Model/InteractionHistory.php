<?php

namespace Modules\CRM\Sales\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class InteractionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'channel',
        'content',
        'interaction_date'
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
        // return \Modules\CRM\Database\factories\InteractionHistoryFactory::new();
    }
}