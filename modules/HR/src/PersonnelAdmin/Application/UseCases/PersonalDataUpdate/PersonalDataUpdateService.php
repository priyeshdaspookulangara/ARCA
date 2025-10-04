<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\PersonalDataUpdate;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;
use Modules\HR\PersonnelAdmin\Domain\Events\EmployeePersonalDataUpdatedEvent;
use Illuminate\Contracts\Events\Dispatcher;

class PersonalDataUpdateService
{
    private $employeeRepository;
    private $eventDispatcher;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, Dispatcher $eventDispatcher)
    {
        $this->employeeRepository = $employeeRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function updatePersonalData(string $employeeId, array $data): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        $setters = [
            'address' => 'setAddress',
            'marital_status' => 'setMaritalStatus',
            'last_name' => 'setLastName',
            'emergency_contact' => 'setEmergencyContact',
            'bank_details' => 'setBankDetails',
        ];

        $updated = false;
        foreach ($setters as $key => $setter) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if ($key === 'bank_details' && is_array($value)) {
                    $value = json_encode($value);
                }
                $employee->{$setter}($value);
                $updated = true;
            }
        }

        if ($updated) {
            $this->employeeRepository->save($employee);
            $this->eventDispatcher->dispatch(new EmployeePersonalDataUpdatedEvent($employee->getId(), $data));
        }

        return $employee;
    }
}