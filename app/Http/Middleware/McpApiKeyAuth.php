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
        [$apiKey, $apiSecret] = $this->extractCredentials($request);

        if (! $apiKey || ! $apiSecret) {
            return response()
                ->json(['error' => 'Missing or invalid API credentials'], Response::HTTP_UNAUTHORIZED)
                ->withHeaders([
                    'WWW-Authenticate' => 'Basic realm="Combell MCP", charset="UTF-8"',
                ]);
        }

        // Normalise credentials so downstream services (Combell binding) can resolve them consistently.
        $request->headers->set('X-API-Key', $apiKey);
        $request->headers->set('X-API-Secret', $apiSecret);

        return $next($request);
    }

    /**
     * Extract the API credentials from the request.
     *
     * Supports HTTP Basic auth (recommended for MCP remote servers) and
     * falls back to explicit headers for local development.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function extractCredentials(Request $request): array
    {
        $basic = $request->header('Authorization');

        if ($basic && str_starts_with($basic, 'Basic ')) {
            $decoded = base64_decode(mb_substr($basic, 6), true);

            if ($decoded !== false && str_contains($decoded, ':')) {
                [$key, $secret] = explode(':', $decoded, 2);

                return [trim($key) ?: null, trim($secret) ?: null];
            }
        }

        return [
            $request->header('X-API-Key'),
            $request->header('X-API-Secret'),
        ];
    }
}
