<?php

use App\Mcp\Servers\CombellServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local("combell", CombellServer::class);
Mcp::web("/mcp/combell", CombellServer::class);
