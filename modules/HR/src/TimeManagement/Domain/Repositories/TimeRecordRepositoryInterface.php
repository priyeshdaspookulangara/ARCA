<?php

namespace Modules\HR\TimeManagement\Domain\Repositories;

use Modules\HR\TimeManagement\Domain\Entities\TimeRecord;

interface TimeRecordRepositoryInterface
{
    public function findById(string $id): ?TimeRecord;

    public function findByEmployee(string $employeeId): array;

    public function save(TimeRecord $timeRecord): void;

    public function delete(string $id): void;
}