<?php

namespace Modules\SD\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SD\Models\SalesOrder;

class SalesOrderCreatedEvent
{
    use Dispatchable, SerializesModels;

    public SalesOrder $salesOrder;

    /**
     * Create a new event instance.
     *
     * @param SalesOrder $salesOrder
     */
    public function __construct(SalesOrder $salesOrder)
    {
        $this->salesOrder = $salesOrder;
    }
}