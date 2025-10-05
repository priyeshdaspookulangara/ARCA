<?php

namespace Modules\HR\TimeManagement\Domain\Repositories;

use Modules\HR\TimeManagement\Domain\Entities\Absence;

interface AbsenceRepositoryInterface
{
    public function findById(string $id): ?Absence;

    public function findByEmployee(string $employeeId): array;

    public function save(Absence $absence): void;

    public function delete(string $id): void;
}