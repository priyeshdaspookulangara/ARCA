<?php

namespace Modules\AuthMgt\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\AuthMgt\Domain\Entities\AuditLog;

class AuditLogController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(AuditLog::latest()->paginate());
    }
}