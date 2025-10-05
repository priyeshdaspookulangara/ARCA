<?php

namespace Modules\HR\TimeManagement\Infrastructure\Persistence;

use Modules\HR\TimeManagement\Domain\Entities\Absence;
use Modules\HR\TimeManagement\Domain\Repositories\AbsenceRepositoryInterface;

class InMemoryAbsenceRepository implements AbsenceRepositoryInterface
{
    private $absences = [];

    public function findById(string $id): ?Absence
    {
        return $this->absences[$id] ?? null;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter($this->absences, function (Absence $absence) use ($employeeId) {
            return $absence->getEmployeeId() === $employeeId;
        });
    }

    public function save(Absence $absence): void
    {
        $this->absences[$absence->getId()] = $absence;
    }

    public function delete(string $id): void
    {
        unset($this->absences[$id]);
    }
}