<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\AuthMgt\Domain\Entities\AuthObject;
use Modules\AuthMgt\Http\Requests\StoreAuthObjectRequest;
use Modules\AuthMgt\Http\Requests\UpdateAuthObjectRequest;

class AuthObjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(AuthObject::all());
    }

    public function store(StoreAuthObjectRequest $request): JsonResponse
    {
        $authObject = AuthObject::create($request->validated());
        return response()->json($authObject, 201);
    }

    public function show(AuthObject $authObject): JsonResponse
    {
        return response()->json($authObject);
    }

    public function update(UpdateAuthObjectRequest $request, AuthObject $authObject): JsonResponse
    {
        $authObject->update($request->validated());
        return response()->json($authObject);
    }

    public function destroy(AuthObject $authObject): JsonResponse
    {
        $authObject->delete();
        return response()->json(null, 204);
    }
}