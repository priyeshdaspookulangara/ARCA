<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\WorkScheduleChange;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;
use Modules\HR\PersonnelAdmin\Domain\Events\WorkScheduleChangedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class WorkScheduleChangeService
{
    private $employeeRepository;
    private $eventDispatcher;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, Dispatcher $eventDispatcher)
    {
        $this->employeeRepository = $employeeRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function changeWorkSchedule(string $employeeId, array $data): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $updated = false;
        if (isset($data['work_schedule'])) {
            $employee->setWorkSchedule($data['work_schedule']);
            $updated = true;
        }

        if (isset($data['employment_type'])) {
            $employee->setEmploymentType($data['employment_type']);
            $updated = true;
        }

        if ($updated) {
            $this->employeeRepository->save($employee);
            $this->eventDispatcher->dispatch(new WorkScheduleChangedEvent($employee->getId(), $data));
        }

        return $employee;
    }
}