<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveSite
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read X-Site-Domain header
        $siteDomain = $request->header('X-Site-Domain');

        if (!$siteDomain) {
            return errorResponse('X-Site-Domain header is required', 400);
        }
        
        $site = Site::where('domain', $siteDomain)
            ->where('is_active', true)
            ->first();

        if (!$site) {
            return errorResponse('Invalid or inactive site', 400);
        }

        $request->merge(['site' => $site]);

        return $next($request);
    }
}
