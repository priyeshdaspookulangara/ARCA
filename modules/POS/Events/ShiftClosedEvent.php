<?php

namespace Modules\POS\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\POS\Models\POSShift;

class ShiftClosedEvent
{
    use Dispatchable, SerializesModels;

    public POSShift $shift;

    /**
     * Create a new event instance.
     *
     * @param POSShift $shift
     */
    public function __construct(POSShift $shift)
    {
        $this->shift = $shift;
    }
}