<?php

namespace Modules\HR\PersonnelAdmin\Domain\Repositories;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;

interface EmployeeRepositoryInterface
{
    public function findById(string $employeeId): ?Employee;

    public function save(Employee $employee): void;
}