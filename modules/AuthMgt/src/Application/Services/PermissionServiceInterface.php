<?php

namespace Modules\AuthMgt\Application\Services;

interface PermissionServiceInterface
{
    /**
     * Assign a permission to a role.
     *
     * @param int $roleId
     * @param int $authObjectId
     * @param array $actions
     * @return bool
     */
    public function assignPermissionToRole(int $roleId, int $authObjectId, array $actions): bool;

    /**
     * Revoke a permission from a role.
     *
     * @param int $roleId
     * @param int $authObjectId
     * @return bool
     */
    public function revokePermissionFromRole(int $roleId, int $authObjectId): bool;
}