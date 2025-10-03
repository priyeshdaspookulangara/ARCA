<?php

namespace Modules\HR\PersonnelAdmin\Application\UseCases\PersonalDataUpdate;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;
use Modules\HR\PersonnelAdmin\Domain\Exceptions\EmployeeNotFoundException;

class PersonalDataUpdateService
{
    private $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function updatePersonalData(string $employeeId, array $data): Employee
    {
        $employee = $this->employeeRepository->findById($employeeId);

        if (!$employee) {
            throw new EmployeeNotFoundException($employeeId);
        }

        if (isset($data['address'])) {
            $employee->setAddress($data['address']);
        }

        if (isset($data['marital_status'])) {
            $employee->setMaritalStatus($data['marital_status']);
        }

        if (isset($data['last_name'])) {
            $employee->setLastName($data['last_name']);
        }

        if (isset($data['emergency_contact'])) {
            $employee->setEmergencyContact($data['emergency_contact']);
        }

        if (isset($data['bank_details'])) {
            $employee->setBankDetails($data['bank_details']);
        }

        $this->employeeRepository->save($employee);

        return $employee;
    }
}