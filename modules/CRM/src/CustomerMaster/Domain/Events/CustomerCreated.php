<?php

namespace Modules\CRM\CustomerMaster\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\CustomerMaster\Domain\Model\Customer;

class CustomerCreated
{
    use Dispatchable, SerializesModels;

    public $customer;

    /**
     * Create a new event instance.
     *
     * @param  \Modules\CRM\CustomerMaster\Domain\Model\Customer  $customer
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}