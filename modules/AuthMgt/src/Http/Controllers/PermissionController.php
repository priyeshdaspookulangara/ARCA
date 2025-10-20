<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AuthMgt\Application\Services\PermissionServiceInterface;
use Modules\AuthMgt\Application\Services\AuthServiceInterface;

class PermissionController extends Controller
{
    protected $permissionService;
    protected $authService;

    public function __construct(PermissionServiceInterface $permissionService, AuthServiceInterface $authService)
    {
        $this->permissionService = $permissionService;
        $this->authService = $authService;
    }

    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'auth_object_id' => 'required|integer|exists:auth_objects,id',
            'actions' => 'required|array',
        ]);

        $this->permissionService->assignPermissionToRole(
            $request->role_id,
            $request->auth_object_id,
            $request->actions
        );

        return response()->json(['message' => 'Permission assigned successfully.']);
    }

    public function revoke(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'auth_object_id' => 'required|integer|exists:auth_objects,id',
        ]);

        $this->permissionService->revokePermissionFromRole(
            $request->role_id,
            $request->auth_object_id
        );

        return response()->json(['message' => 'Permission revoked successfully.']);
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'object_code' => 'required|string',
            'action' => 'required|string',
        ]);

        $hasAccess = $this->authService->checkAccess(
            $request->user_id,
            $request->object_code,
            $request->action
        );

        return response()->json(['authorized' => $hasAccess]);
    }
}