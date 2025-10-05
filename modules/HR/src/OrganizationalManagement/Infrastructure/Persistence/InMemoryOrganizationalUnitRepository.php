<?php

namespace Modules\HR\OrganizationalManagement\Infrastructure\Persistence;

use Modules\HR\OrganizationalManagement\Domain\Entities\OrganizationalUnit;
use Modules\HR\OrganizationalManagement\Domain\Repositories\OrganizationalUnitRepositoryInterface;

class InMemoryOrganizationalUnitRepository implements OrganizationalUnitRepositoryInterface
{
    private $orgUnits = [];

    public function findById(string $id): ?OrganizationalUnit
    {
        return $this->orgUnits[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->orgUnits);
    }

    public function save(OrganizationalUnit $orgUnit): void
    {
        $this->orgUnits[$orgUnit->getId()] = $orgUnit;
    }

    public function delete(string $id): void
    {
        unset($this->orgUnits[$id]);
    }
}