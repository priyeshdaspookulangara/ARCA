<?php

namespace Modules\GRC\AccessControl\Application;

use App\Models\User;

class AccessControlService
{
    /**
     * Check if a user has a specific permission.
     *
     * @param \App\Models\User $user
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(User $user, string $permissionName): bool
    {
        // In a real implementation, this would check the user's roles and permissions.
        // For now, we will return true for all checks.
        return true;
    }
}