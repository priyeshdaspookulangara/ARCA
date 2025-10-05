<?php

namespace Modules\HR\OrganizationalManagement\Application\Services;

use Modules\HR\OrganizationalManagement\Domain\Entities\OrganizationalUnit;
use Modules\HR\OrganizationalManagement\Domain\Repositories\OrganizationalUnitRepositoryInterface;

class OrganizationalUnitService
{
    private $orgUnitRepository;

    public function __construct(OrganizationalUnitRepositoryInterface $orgUnitRepository)
    {
        $this->orgUnitRepository = $orgUnitRepository;
    }

    public function createOrganizationalUnit(string $name, ?string $parentId = null): OrganizationalUnit
    {
        $id = uniqid('org_');
        $orgUnit = new OrganizationalUnit($id, $name, $parentId);
        $this->orgUnitRepository->save($orgUnit);
        return $orgUnit;
    }

    public function getOrganizationalUnit(string $id): ?OrganizationalUnit
    {
        return $this->orgUnitRepository->findById($id);
    }

    public function getAllOrganizationalUnits(): array
    {
        return $this->orgUnitRepository->findAll();
    }

    public function deleteOrganizationalUnit(string $id): void
    {
        $this->orgUnitRepository->delete($id);
    }
}