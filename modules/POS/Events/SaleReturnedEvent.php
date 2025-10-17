<?php

namespace Modules\POS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\POS\Models\Sale;

class SaleReturnedEvent
{
    use Dispatchable, SerializesModels;

    public Sale $sale;

    /**
     * Create a new event instance.
     *
     * @param Sale $sale
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }
}