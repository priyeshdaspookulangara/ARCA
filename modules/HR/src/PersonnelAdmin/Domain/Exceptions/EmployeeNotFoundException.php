<?php

namespace Modules\HR\PersonnelAdmin\Domain\Exceptions;

class EmployeeNotFoundException extends \Exception
{
    public function __construct(string $employeeId)
    {
        parent::__construct("Employee with ID '{$employeeId}' not found.");
    }
}