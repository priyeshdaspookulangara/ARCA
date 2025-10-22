<?php

namespace Modules\Payments\Listeners;

use Modules\Payments\Events\PaymentCompleted;

class NotifyCustomer
{
    public function __construct()
    {
        //
    }

    public function handle(PaymentCompleted $event)
    {
        // Logic to notify the customer about the completed payment
    }
}
