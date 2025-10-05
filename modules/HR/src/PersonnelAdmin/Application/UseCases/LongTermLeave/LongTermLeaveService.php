<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\LongTermLeave;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;
use Modules\HR\PersonnelAdmin\Domain\Events\LongTermLeaveStartedEvent;
use Modules\HR\PersonnelAdmin\Domain\Events\LongTermLeaveEndedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class LongTermLeaveService
{
    private $employeeRepository;
    private $eventDispatcher;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, Dispatcher $eventDispatcher)
    {
        $this->employeeRepository = $employeeRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function startLeave(string $employeeId, string $leaveType): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $employee->setOnLeave(true);
        $employee->setLeaveType($leaveType);

        $this->employeeRepository->save($employee);

        $this->eventDispatcher->dispatch(new LongTermLeaveStartedEvent($employee->getId(), $employee->getLeaveType()));

        return $employee;
    }

    public function endLeave(string $employeeId): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $employee->setOnLeave(false);
        $employee->setLeaveType(null);

        $this->employeeRepository->save($employee);

        $this->eventDispatcher->dispatch(new LongTermLeaveEndedEvent($employee->getId()));

        return $employee;
    }
}