<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;

class SalaryChangeService
{
    private $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function changeSalary(string $employeeId, float $newSalary): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $employee->setSalary($newSalary);

        $this->employeeRepository->save($employee);

        return $employee;
    }
}