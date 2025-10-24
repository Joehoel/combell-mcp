<?php

declare(strict_types=1);

use App\Mcp\Servers\CombellServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('combell', CombellServer::class);

Mcp::web('/mcp', CombellServer::class)
    ->middleware(['mcp.auth']);
