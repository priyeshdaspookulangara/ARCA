<?php

namespace Modules\CRM\Service\Domain\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class ServiceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to'
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
        // return \Modules\CRM\Database\factories\ServiceTicketFactory::new();
    }
}