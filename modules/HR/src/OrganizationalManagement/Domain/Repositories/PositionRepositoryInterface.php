<?php

namespace Modules\HR\OrganizationalManagement\Domain\Repositories;

use Modules\HR\OrganizationalManagement\Domain\Entities\Position;

interface PositionRepositoryInterface
{
    public function findById(string $id): ?Position;

    public function findAll(): array;

    public function save(Position $position): void;

    public function delete(string $id): void;
}