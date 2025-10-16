<?php

namespace Modules\SD\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SD\Models\Delivery;

class DeliveryCompletedEvent
{
    use Dispatchable, SerializesModels;

    public Delivery $delivery;

    /**
     * Create a new event instance.
     *
     * @param Delivery $delivery
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }
}