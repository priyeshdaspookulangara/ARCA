<?php

namespace Modules\MM\InventoryManagement\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\SD\Events\SalesOrderCreatedEvent;

class ReserveStockListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SalesOrderCreatedEvent  $event
     * @return void
     */
    public function handle(SalesOrderCreatedEvent $event)
    {
        // Logic to reserve stock for the sales order
        \Log::info("MM Listener: SalesOrderCreatedEvent handled for SO #{$event->salesOrder->id}.");
    }
}