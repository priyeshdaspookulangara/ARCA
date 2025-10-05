<?php

namespace Modules\HR\OrganizationalManagement\Domain\Repositories;

use Modules\HR\OrganizationalManagement\Domain\Entities\OrganizationalUnit;

interface OrganizationalUnitRepositoryInterface
{
    public function findById(string $id): ?OrganizationalUnit;

    public function findAll(): array;

    public function save(OrganizationalUnit $orgUnit): void;

    public function delete(string $id): void;
}