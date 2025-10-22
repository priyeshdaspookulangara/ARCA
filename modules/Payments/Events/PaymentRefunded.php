<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;

class PaymentRefunded
{
    use SerializesModels;

    public $refund;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($refund)
    {
        $this->refund = $refund;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
