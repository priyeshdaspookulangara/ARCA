<?php

namespace Modules\MM\InventoryManagement\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\POS\Events\SaleCompletedEvent;

class DeductStockListener implements ShouldQueue
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
     * @param  SaleCompletedEvent  $event
     * @return void
     */
    public function handle(SaleCompletedEvent $event)
    {
        // Logic to deduct stock for the sale
        \Log::info("MM Listener: SaleCompletedEvent handled for Sale #{$event->sale->id}.");
    }
}