<?php

namespace Modules\MM\InventoryManagement\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\SD\Events\DeliveryCompletedEvent;

class TriggerGoodsIssueListener implements ShouldQueue
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
     * @param  DeliveryCompletedEvent  $event
     * @return void
     */
    public function handle(DeliveryCompletedEvent $event)
    {
        // Logic to trigger goods issue for the delivery
        \Log::info("MM Listener: DeliveryCompletedEvent handled for Delivery #{$event->delivery->id}.");
    }
}