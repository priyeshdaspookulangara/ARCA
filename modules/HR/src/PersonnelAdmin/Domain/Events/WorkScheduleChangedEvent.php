<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkScheduleChangedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $changedData;

    /**
     * Create a new event instance.
     *
     * @param string $employeeId
     * @param array $changedData
     */
    public function __construct(string $employeeId, array $changedData)
    {
        $this->employeeId = $employeeId;
        $this->changedData = $changedData;
    }
}