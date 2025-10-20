<?php

namespace Modules\AuthMgt\Application\Services;

use Illuminate\Support\Facades\Cache;
use Modules\AuthMgt\Domain\Entities\AuthObject;
use Modules\AuthMgt\Domain\Entities\AuthUser;
use Modules\AuthMgt\Domain\Entities\Role;
use App\Models\User;
use Modules\AuthMgt\Application\Events\RoleAssigned;
use Modules\AuthMgt\Application\Events\RoleRevoked;
use Modules\AuthMgt\Domain\Entities\AuditLog;

class AuthService implements AuthServiceInterface
{
    public function assignRoleToUser(int $userId, int $roleId): bool
    {
        $user = User::find($userId);
        $role = Role::find($roleId);

        if (!$user || !$role) {
            return false;
        }

        $authUser = AuthUser::firstOrCreate(['user_id' => $userId]);
        $authUser->roles()->syncWithoutDetaching($roleId);

        Cache::forget('user-permissions-' . $userId);
        event(new RoleAssigned($userId, $roleId));

        AuditLog::create([
            'event_type' => 'role_assigned',
            'user_id' => auth()->id(),
            'auditable_id' => $userId,
            'auditable_type' => User::class,
            'details' => "Role {$role->name} assigned to user {$user->name}",
        ]);

        return true;
    }

    public function revokeRoleFromUser(int $userId, int $roleId): bool
    {
        $user = User::find($userId);
        $role = Role::find($roleId);

        if (!$user || !$role) {
            return false;
        }

        $authUser = AuthUser::where('user_id', $userId)->first();

        if ($authUser) {
            $authUser->roles()->detach($roleId);
            Cache::forget('user-permissions-' . $userId);
            event(new RoleRevoked($userId, $roleId));

            AuditLog::create([
                'event_type' => 'role_revoked',
                'user_id' => auth()->id(),
                'auditable_id' => $userId,
                'auditable_type' => User::class,
                'details' => "Role {$role->name} revoked from user {$user->name}",
            ]);
        }

        return true;
    }

    public function registerAuthObjects(array $objects): void
    {
        foreach ($objects as $objectData) {
            AuthObject::updateOrCreate(
                ['code' => $objectData['code']],
                $objectData
            );
        }
    }

    public function checkAccess(int $userId, string $objectCode, string $action): bool
    {
        $userPermissions = Cache::remember('user-permissions-' . $userId, now()->addMinutes(60), function () use ($userId) {
            $authUser = AuthUser::with('roles.permissions')->where('user_id', $userId)->first();
            if (!$authUser) {
                return [];
            }

            $permissions = [];
            foreach ($authUser->roles as $role) {
                foreach ($role->permissions as $permission) {
                    $actions = is_string($permission->pivot->actions) ? json_decode($permission->pivot->actions, true) : $permission->pivot->actions;

                    if (!isset($permissions[$permission->code])) {
                        $permissions[$permission->code] = [];
                    }

                    $permissions[$permission->code] = array_merge($permissions[$permission->code], $actions);
                }
            }
            return $permissions;
        });

        return isset($userPermissions[$objectCode]) && in_array($action, $userPermissions[$objectCode]);
    }
}