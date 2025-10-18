<?php

namespace Modules\GRC\AccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GRC\AccessControl\Domain\PermissionRepositoryInterface;
use Modules\GRC\AccessControl\Domain\Model\Permission;

class PermissionController extends Controller
{
    private $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function index()
    {
        return $this->permissionRepository->getAll();
    }

    public function store(Request $request)
    {
        $permission = new Permission($request->all());
        return $this->permissionRepository->save($permission);
    }

    public function show($id)
    {
        return $this->permissionRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $permission = $this->permissionRepository->findById($id);
        $permission->fill($request->all());
        return $this->permissionRepository->save($permission);
    }

    public function destroy($id)
    {
        $permission = $this->permissionRepository->findById($id);
        return response()->json(['success' => $this->permissionRepository->delete($permission)]);
    }
}