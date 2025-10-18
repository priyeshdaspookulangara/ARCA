<?php

namespace Modules\GRC\AccessControl\Infrastructure\Persistence;

use Illuminate\Support\Collection;
use Modules\GRC\AccessControl\Domain\PermissionRepositoryInterface;
use Modules\GRC\AccessControl\Domain\Model\Permission;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function getAll(): Collection
    {
        return Permission::all();
    }

    public function save(Permission $permission): Permission
    {
        $permission->save();
        return $permission;
    }

    public function delete(Permission $permission): bool
    {
        return $permission->delete();
    }
}