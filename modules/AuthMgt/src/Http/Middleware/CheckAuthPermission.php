<?php

namespace Modules\AuthMgt\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AuthMgt\Application\Services\AuthServiceInterface;

class CheckAuthPermission
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $objectCode
     * @param  string  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $objectCode, string $action)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $userId = Auth::id();
        if (!$this->authService->checkAccess($userId, $objectCode, $action)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}