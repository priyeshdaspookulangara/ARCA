<?php

namespace Modules\AuthMgt\Application\Services;

use Modules\AuthMgt\Domain\Entities\Role;
use Modules\AuthMgt\Domain\Entities\AuthObject;
use Modules\AuthMgt\Application\Events\PermissionUpdated;
use Modules\AuthMgt\Domain\Entities\AuditLog;

class PermissionService implements PermissionServiceInterface
{
    public function assignPermissionToRole(int $roleId, int $authObjectId, array $actions): bool
    {
        $role = Role::find($roleId);
        $authObject = AuthObject::find($authObjectId);

        if (!$role || !$authObject) {
            return false;
        }

        $role->permissions()->syncWithoutDetaching([$authObjectId => ['actions' => json_encode($actions)]]);

        event(new PermissionUpdated($roleId));

        AuditLog::create([
            'event_type' => 'permission_assigned',
            'user_id' => auth()->id(),
            'auditable_id' => $roleId,
            'auditable_type' => Role::class,
            'details' => "Permission {$authObject->code} assigned to role {$role->name}",
        ]);

        return true;
    }

    public function revokePermissionFromRole(int $roleId, int $authObjectId): bool
    {
        $role = Role::find($roleId);
        $authObject = AuthObject::find($authObjectId);

        if (!$role || !$authObject) {
            return false;
        }

        $role->permissions()->detach($authObjectId);

        event(new PermissionUpdated($roleId));

        AuditLog::create([
            'event_type' => 'permission_revoked',
            'user_id' => auth()->id(),
            'auditable_id' => $roleId,
            'auditable_type' => Role::class,
            'details' => "Permission {$authObject->code} revoked from role {$role->name}",
        ]);

        return true;
    }
}