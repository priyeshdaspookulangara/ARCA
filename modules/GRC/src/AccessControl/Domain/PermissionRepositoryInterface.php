<?php

namespace Modules\GRC\AccessControl\Domain;

use Illuminate\Support\Collection;
use Modules\GRC\AccessControl\Domain\Model\Permission;

interface PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission;

    public function getAll(): Collection;

    public function save(Permission $permission): Permission;

    public function delete(Permission $permission): bool;
}