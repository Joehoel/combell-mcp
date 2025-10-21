<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AccountsTool;
use App\Mcp\Tools\AccountTool;
use App\Mcp\Tools\DatabaseTool;
use App\Mcp\Tools\LinuxHostingsTool;
use App\Mcp\Tools\LinuxHostingTool;
use Laravel\Mcp\Server;

class CombellServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Combell Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        This server brokers read-only access to the Combell API through the official PHP SDK. Choose tools based on the information you need:

        - `accounts`: list Combell accounts with optional `asset_type` or `identifier` filters; returns an array with each account's `id`, `identifier`, and `servicepack_id` with pagination handled automatically.
        - `account`: provide an account `domain` (identifier) to fetch the matching account record.
        - `linux_hostings`: enumerate every Linux hosting without requesting additional pages; use this when you need possible domains to inspect further.
        - `linux_hosting`: supply a hosting `domain` to retrieve full configuration details (IP, FTP/SSH credentials, PHP version, subsites, database names, usage limits).
        - `database`: give a MySQL `database_name` to obtain size metrics, hostnames, user counts, and owning `account_id`.

        Always prefer the most specific tool that answers the question. Returned payloads mirror the Combell API responses, so field names follow the snake_case format from the platform.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [AccountsTool::class, AccountTool::class, LinuxHostingsTool::class, LinuxHostingTool::class, DatabaseTool::class];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
