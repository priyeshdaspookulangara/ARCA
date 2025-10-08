<?php

namespace Modules\HR\Benefits\Domain\Repositories;

use Modules\HR\Benefits\Domain\Entities\EmployeeEnrollment;

interface EmployeeEnrollmentRepositoryInterface
{
    public function findById(string $id): ?EmployeeEnrollment;

    public function findByEmployee(string $employeeId): array;

    public function save(EmployeeEnrollment $enrollment): void;
}