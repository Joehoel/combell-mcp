<?php

declare(strict_types=1);

use App\Http\Middleware\McpApiKeyAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

test('rejects requests without credentials and advertises basic auth', function () {
    Route::post('/auth-probe', fn (): mixed => response()->json(['ok' => true]))
        ->middleware(McpApiKeyAuth::class);

    $response = $this->postJson('/auth-probe');

    $response->assertUnauthorized();
    $response->assertHeader('WWW-Authenticate', 'Basic realm="Combell MCP", charset="UTF-8"');
    $response->assertJson(['error' => 'Missing or invalid API credentials']);
});

test('accepts http basic credentials and normalises headers', function () {
    Route::post('/auth-probe', function (Request $request): mixed {
        return response()->json([
            'key' => $request->header('X-API-Key'),
            'secret' => $request->header('X-API-Secret'),
        ]);
    })->middleware(McpApiKeyAuth::class);

    $response = $this->postJson('/auth-probe', [], [
        'Authorization' => 'Basic '.base64_encode('token-value:secret-value'),
    ]);

    $response->assertOk();
    $response->assertJson([
        'key' => 'token-value',
        'secret' => 'secret-value',
    ]);
});

test('accepts explicit header credentials for local usage', function () {
    Route::post('/auth-probe', function (Request $request): mixed {
        return response()->json([
            'key' => $request->header('X-API-Key'),
            'secret' => $request->header('X-API-Secret'),
        ]);
    })->middleware(McpApiKeyAuth::class);

    $response = $this->postJson('/auth-probe', [], [
        'X-API-Key' => 'header-key',
        'X-API-Secret' => 'header-secret',
    ]);

    $response->assertOk();
    $response->assertJson([
        'key' => 'header-key',
        'secret' => 'header-secret',
    ]);
});
