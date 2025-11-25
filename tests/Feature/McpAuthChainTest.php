<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Route::post('/auth-chain', fn (Request $request): mixed => response()->json([
        'ok' => true,
        'key' => $request->header('X-API-Key'),
    ]))->middleware(['auth:api', 'mcp.auth']);
});

it('rejects requests without bearer token', function () {
    $response = $this->postJson('/auth-chain');

    $response->assertUnauthorized();
});

it('rejects requests without API credentials even when authenticated', function () {
    Passport::actingAs(user: App\Models\User::factory()->create(), scopes: ['mcp:use']);

    $response = $this->postJson('/auth-chain');

    $response->assertUnauthorized();
    $response->assertJson(['error' => 'Missing or invalid API credentials']);
});

it('allows authenticated requests with API credentials', function () {
    Passport::actingAs(user: App\Models\User::factory()->create(), scopes: ['mcp:use']);

    $response = $this->postJson('/auth-chain', [], [
        'X-API-Key' => 'token',
        'X-API-Secret' => 'secret',
    ]);

    $response->assertOk();
    $response->assertJson([
        'ok' => true,
        'key' => 'token',
    ]);
});
