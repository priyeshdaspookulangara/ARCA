<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\SalaryChange;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;
use Modules\HR\PersonnelAdmin\Domain\Events\EmployeeSalaryUpdatedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class SalaryChangeService
{
    private $employeeRepository;
    private $eventDispatcher;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, Dispatcher $eventDispatcher)
    {
        $this->employeeRepository = $employeeRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function changeSalary(string $employeeId, float $newSalary): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $employee->setSalary($newSalary);

        $this->employeeRepository->save($employee);

        $this->eventDispatcher->dispatch(new EmployeeSalaryUpdatedEvent($employee->getId(), $employee->getSalary()));

        return $employee;
    }
}