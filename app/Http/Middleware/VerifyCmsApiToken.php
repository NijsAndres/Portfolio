<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the internal /api/cms endpoints (Step 12) with a static Bearer token.
 *
 * The token lives in config('services.cms.api_token') (CMS_API_TOKEN in .env)
 * and is shared with the MCP server. Requests must send
 * `Authorization: Bearer {token}`; anything else gets a 401 JSON response.
 */
class VerifyCmsApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.cms.api_token');
        $provided = $request->bearerToken();

        // Refuse if the server has no token configured, or the header is missing
        // or wrong. hash_equals keeps the comparison timing-safe.
        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            abort(401, 'Invalid API token.');
        }

        return $next($request);
    }
}
