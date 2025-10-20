<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\AuthMgt\Domain\Entities\Role;
use Modules\AuthMgt\Http\Requests\StoreRoleRequest;
use Modules\AuthMgt\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Role::all());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());
        return response()->json($role, 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json($role);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());
        return response()->json($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return response()->json(null, 204);
    }
}