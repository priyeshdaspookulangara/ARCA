<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LongTermLeaveEndedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;

    /**
     * Create a new event instance.
     *
     * @param string $employeeId
     */
    public function __construct(string $employeeId)
    {
        $this->employeeId = $employeeId;
    }
}