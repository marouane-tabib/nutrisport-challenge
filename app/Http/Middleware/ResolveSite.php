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

        // Check if header is missing
        if (!$siteDomain) {
            return errorResponse('X-Site-Domain header is required', 400);
        }
        
        // Find site by domain and is_active status
        $site = Site::where('domain', $siteDomain)
            ->where('is_active', true)
            ->first();

        // Check if site not found
        if (!$site) {
            return errorResponse('Invalid or inactive site', 400);
        }

        // Merge site into request
        $request->merge(['site' => $site]);

        return $next($request);
    }
}
