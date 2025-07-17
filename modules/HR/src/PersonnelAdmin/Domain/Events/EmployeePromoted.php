<?php

namespace Modules\HR\PersonnelAdmin\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeePromoted
{
    use Dispatchable, SerializesModels;

    public $employeeId;
    public $personnelActionRequestId;

    public function __construct(int $employeeId, int $personnelActionRequestId)
    {
        $this->employeeId = $employeeId;
        $this->personnelActionRequestId = $personnelActionRequestId;
    }
}
