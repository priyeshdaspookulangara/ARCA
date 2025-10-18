<?php

namespace Modules\Analytics\SharedKernel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCompletedEvent
{
    use Dispatchable, SerializesModels;

    public $saleData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $saleData)
    {
        $this->saleData = $saleData;
    }
}