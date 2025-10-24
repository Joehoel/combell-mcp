<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class McpApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $apiSecret = $request->header('X-API-Secret');

        if (! $apiKey || ! $apiSecret) {
            return response()->json(['error' => 'Missing API credentials'], 401);
        }

        if ($apiKey !== config('app.mcp_api_key') || $apiSecret !== config('app.mcp_api_secret')) {
            return response()->json(['error' => 'Invalid API credentials'], 403);
        }

        return $next($request);
    }
}
