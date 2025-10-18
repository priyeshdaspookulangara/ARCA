<?php

namespace Modules\Analytics\Facts\Application\Listeners;

use Modules\Analytics\SharedKernel\Events\SaleCompletedEvent;
use Modules\Analytics\Facts\Domain\Model\FactsSales;

class IngestSaleData
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
     * @param  \Modules\Analytics\SharedKernel\Events\SaleCompletedEvent  $event
     * @return void
     */
    public function handle(SaleCompletedEvent $event)
    {
        FactsSales::create($event->saleData);
    }
}