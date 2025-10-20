<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AuthMgt\Application\Services\AuthServiceInterface;

class UserRoleController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function store(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $this->authService->assignRoleToUser($userId, $request->role_id);

        return response()->json(['message' => 'Role assigned successfully.']);
    }

    public function destroy(int $userId, int $roleId): JsonResponse
    {
        $this->authService->revokeRoleFromUser($userId, $roleId);

        return response()->json(['message' => 'Role revoked successfully.']);
    }
}