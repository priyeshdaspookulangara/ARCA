<?php

namespace Modules\ARCA_API_Gateway\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        Log::info('API Request:', [
            'method' => $request->getMethod(),
            'url' => $request->getUri(),
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
        ]);

        return $response;
    }
}
