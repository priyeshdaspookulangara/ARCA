<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeePersonalDataUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $updatedData;

    /**
     * Create a new event instance.
     *
     * @param string $employeeId
     * @param array $updatedData
     */
    public function __construct(string $employeeId, array $updatedData)
    {
        $this->employeeId = $employeeId;
        $this->updatedData = $updatedData;
    }
}