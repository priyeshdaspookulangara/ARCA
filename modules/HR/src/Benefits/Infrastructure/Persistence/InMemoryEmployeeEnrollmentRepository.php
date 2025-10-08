<?php

namespace Modules\HR\Benefits\Infrastructure\Persistence;

use Modules\HR\Benefits\Domain\Entities\EmployeeEnrollment;
use Modules\HR\Benefits\Domain\Repositories\EmployeeEnrollmentRepositoryInterface;

class InMemoryEmployeeEnrollmentRepository implements EmployeeEnrollmentRepositoryInterface
{
    private $enrollments = [];

    public function findById(string $id): ?EmployeeEnrollment
    {
        return $this->enrollments[$id] ?? null;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter($this->enrollments, function (EmployeeEnrollment $enrollment) use ($employeeId) {
            return $enrollment->getEmployeeId() === $employeeId;
        });
    }

    public function save(EmployeeEnrollment $enrollment): void
    {
        $this->enrollments[$enrollment->getId()] = $enrollment;
    }
}