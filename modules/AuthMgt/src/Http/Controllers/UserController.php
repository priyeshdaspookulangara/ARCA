<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\AuthMgt\Domain\Entities\AuthUser;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(AuthUser::with('roles')->paginate());
    }
}