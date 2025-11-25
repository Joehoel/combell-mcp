<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Fluent\AssertableJson;

it('exposes oauth resource metadata for the MCP server', function () {
    expect(Route::has('mcp.oauth.protected-resource'))->toBeTrue();

    $response = $this->get('.well-known/oauth-protected-resource');

    $response->assertOk();
    $response->assertJson(fn (AssertableJson $json) => $json
        ->where('resource', url('/'))
        ->whereType('authorization_servers', 'array')
        ->etc()
    );
});

it('exposes oauth authorization server metadata for the MCP server', function () {
    expect(Route::has('mcp.oauth.authorization-server'))->toBeTrue();

    $response = $this->get('.well-known/oauth-authorization-server');

    $response->assertOk();
    $response->assertJson(fn (AssertableJson $json) => $json
        ->where('issuer', url('/'))
        ->where('authorization_endpoint', route('passport.authorizations.authorize'))
        ->where('token_endpoint', route('passport.token'))
        ->where('scopes_supported.0', 'mcp:use')
        ->etc()
    );
});
