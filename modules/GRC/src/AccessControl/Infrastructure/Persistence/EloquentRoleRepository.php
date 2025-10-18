<?php

namespace Modules\GRC\AccessControl\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\GRC\AccessControl\Domain\RoleRepositoryInterface;
use Modules\GRC\AccessControl\Domain\Model\Role;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function findById(int $id): ?Role
    {
        return Role::find($id);
    }

    public function getAll(): Collection
    {
        return Role::all();
    }

    public function save(Role $role): Role
    {
        $role->save();
        return $role;
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }
}