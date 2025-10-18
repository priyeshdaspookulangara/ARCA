<?php

namespace Modules\GRC\AccessControl\Domain;

use Illuminate\Support\Collection;
use Modules\GRC\AccessControl\Domain\Model\Role;

interface RoleRepositoryInterface
{
    public function findById(int $id): ?Role;

    public function getAll(): Collection;

    public function save(Role $role): Role;

    public function delete(Role $role): bool;
}