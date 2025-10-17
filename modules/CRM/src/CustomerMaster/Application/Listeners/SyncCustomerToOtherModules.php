<?php

namespace Modules\CRM\CustomerMaster\Application\Listeners;

use Modules\CRM\CustomerMaster\Domain\Events\CustomerCreated;

class SyncCustomerToOtherModules
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
     * @param  \Modules\CRM\CustomerMaster\Domain\Events\CustomerCreated  $event
     * @return void
     */
    public function handle(CustomerCreated $event)
    {
        // Placeholder for logic to sync the new customer to other modules like FINA and SD.
        // For example, you might dispatch another event here that other modules listen to.
        \Log::info("New customer created: {$event->customer->name}. Syncing to other modules.");
    }
}