<?php

namespace Modules\HR\TimeManagement\Infrastructure\Persistence;

use Modules\HR\TimeManagement\Domain\Entities\TimeRecord;
use Modules\HR\TimeManagement\Domain\Repositories\TimeRecordRepositoryInterface;

class InMemoryTimeRecordRepository implements TimeRecordRepositoryInterface
{
    private $timeRecords = [];

    public function findById(string $id): ?TimeRecord
    {
        return $this->timeRecords[$id] ?? null;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter($this->timeRecords, function (TimeRecord $record) use ($employeeId) {
            return $record->getEmployeeId() === $employeeId;
        });
    }

    public function save(TimeRecord $timeRecord): void
    {
        $this->timeRecords[$timeRecord->getId()] = $timeRecord;
    }

    public function delete(string $id): void
    {
        unset($this->timeRecords[$id]);
    }
}