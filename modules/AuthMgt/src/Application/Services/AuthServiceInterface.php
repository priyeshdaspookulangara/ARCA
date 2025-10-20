<?php

namespace Modules\AuthMgt\Application\Services;

interface AuthServiceInterface
{
    /**
     * Assign a role to a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function assignRoleToUser(int $userId, int $roleId): bool;

    /**
     * Revoke a role from a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function revokeRoleFromUser(int $userId, int $roleId): bool;

    /**
     * Register authorization objects from a module.
     *
     * @param array $objects
     * @return void
     */
    public function registerAuthObjects(array $objects): void;

    /**
     * Check if a user has a specific permission.
     *
     * @param int $userId
     * @param string $objectCode
     * @param string $action
     * @return bool
     */
    public function checkAccess(int $userId, string $objectCode, string $action): bool;
}