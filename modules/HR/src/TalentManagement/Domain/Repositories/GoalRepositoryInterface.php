<?php

namespace Modules\HR\TalentManagement\Domain\Repositories;

use Modules\HR\TalentManagement\Domain\Entities\Goal;

interface GoalRepositoryInterface
{
    public function findById(string $id): ?Goal;

    public function findByEmployee(string $employeeId): array;

    public function save(Goal $goal): void;
}