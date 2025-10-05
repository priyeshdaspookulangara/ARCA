<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LongTermLeaveStartedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $leaveType;

    /**
     * Create a new event instance.
     *
     * @param string $employeeId
     * @param string $leaveType
     */
    public function __construct(string $employeeId, string $leaveType)
    {
        $this->employeeId = $employeeId;
        $this->leaveType = $leaveType;
    }
}