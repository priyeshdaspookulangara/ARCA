<?php

namespace Modules\HR\TalentManagement\Infrastructure\Persistence;

use Modules\HR\TalentManagement\Domain\Entities\Goal;
use Modules\HR\TalentManagement\Domain\Repositories\GoalRepositoryInterface;

class InMemoryGoalRepository implements GoalRepositoryInterface
{
    private $goals = [];

    public function findById(string $id): ?Goal
    {
        return $this->goals[$id] ?? null;
    }

    public function findByEmployee(string $employeeId): array
    {
        return array_filter($this->goals, function (Goal $goal) use ($employeeId) {
            return $goal->getEmployeeId() === $employeeId;
        });
    }

    public function save(Goal $goal): void
    {
        $this->goals[$goal->getId()] = $goal;
    }
}