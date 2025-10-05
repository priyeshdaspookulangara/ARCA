<?php

namespace Modules\HR\OrganizationalManagement\Infrastructure\Persistence;

use Modules\HR\OrganizationalManagement\Domain\Entities\Position;
use Modules\HR\OrganizationalManagement\Domain\Repositories\PositionRepositoryInterface;

class InMemoryPositionRepository implements PositionRepositoryInterface
{
    private $positions = [];

    public function findById(string $id): ?Position
    {
        return $this->positions[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->positions);
    }

    public function save(Position $position): void
    {
        $this->positions[$position->getId()] = $position;
    }

    public function delete(string $id): void
    {
        unset($this->positions[$id]);
    }
}