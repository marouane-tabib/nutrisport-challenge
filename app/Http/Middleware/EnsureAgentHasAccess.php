<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgentHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $agent = auth('agent')->user();

        if (!$agent || !$agent->hasFullAccess()) {
            return errorResponse('Insufficient permissions', 403);
        }

        return $next($request);
    }
}
