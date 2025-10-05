<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $newSalary;

    /**
     * Create a new event instance.
     *
     * @param string $employeeId
     * @param float $newSalary
     */
    public function __construct(string $employeeId, float $newSalary)
    {
        $this->employeeId = $employeeId;
        $this->newSalary = $newSalary;
    }
}