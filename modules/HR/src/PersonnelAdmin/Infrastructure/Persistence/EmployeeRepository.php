<?php

namespace Modules\HR\PersonnelAdmin\Infrastructure\Persistence;

use Modules\HR\PersonnelAdmin\Domain\Entities\Employee;
use Modules\HR\PersonnelAdmin\Domain\Repositories\EmployeeRepositoryInterface;

/**
 * NOTE: This is an in-memory implementation of the EmployeeRepositoryInterface for demonstration purposes.
 * In a production environment, this would be replaced with a persistent storage solution,
 * such as a database-backed repository (e.g., using Eloquent).
 */
class EmployeeRepository implements EmployeeRepositoryInterface
{
    private $employees = [];

    public function __construct()
    {
        $employee1 = new Employee('123');
        $employee1->setSalary(50000);
        $employee1->setAddress('123 Main St');
        $employee1->setMaritalStatus('Single');
        $employee1->setLastName('Smith');
        $this->save($employee1);

        $employee2 = new Employee('456');
        $employee2->setSalary(75000);
        $employee2->setAddress('456 Oak Ave');
        $employee2->setMaritalStatus('Married');
        $employee2->setLastName('Jones');
        $this->save($employee2);
    }

    public function findById(string $employeeId): ?Employee
    {
        return $this->employees[$employeeId] ?? null;
    }

    public function save(Employee $employee): void
    {
        $this->employees[$employee->getId()] = $employee;
    }
}