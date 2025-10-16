<?php

namespace Modules\SD\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\SD\Models\Invoice;

class BillingGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public Invoice $invoice;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}