<?php

namespace Modules\GRC\AccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GRC\AccessControl\Domain\RoleRepositoryInterface;
use Modules\GRC\AccessControl\Domain\Model\Role;

class RoleController extends Controller
{
    private $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        return $this->roleRepository->getAll();
    }

    public function store(Request $request)
    {
        $role = new Role($request->all());
        return $this->roleRepository->save($role);
    }

    public function show($id)
    {
        return $this->roleRepository->findById($id);
    }

    public function update(Request $request, $id)
    {
        $role = $this->roleRepository->findById($id);
        $role->fill($request->all());
        return $this->roleRepository->save($role);
    }

    public function destroy($id)
    {
        $role = $this->roleRepository->findById($id);
        return response()->json(['success' => $this->roleRepository->delete($role)]);
    }
}