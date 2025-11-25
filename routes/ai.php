<?php

declare(strict_types=1);

use App\Mcp\Servers\CombellServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::oauthRoutes('oauth');

Mcp::local('combell', CombellServer::class);

Mcp::web('/mcp', CombellServer::class)
    ->middleware(['auth:api', 'mcp.auth']);
