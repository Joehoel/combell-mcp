<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\AccountsTool;
use App\Mcp\Tools\AccountTool;
use App\Mcp\Tools\DatabaseTool;
use App\Mcp\Tools\DnsRecordsTool;
use App\Mcp\Tools\DomainHealthTool;
use App\Mcp\Tools\HostingOverviewTool;
use App\Mcp\Tools\LinuxHostingsTool;
use App\Mcp\Tools\LinuxHostingTool;
use Laravel\Mcp\Server;

final class CombellServer extends Server
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
        This server provides intelligent, actionable insights into your Combell infrastructure. Choose tools based on your management needs:

        **Health & Monitoring:**
        - `domain_health`: Get comprehensive domain health overview including expiration dates, DNS configuration status, and issues requiring attention. Replaces the need for separate domain listing and DNS record checks.
        - `hosting_overview`: Get unified view of all hosting accounts with databases, SSL certificates, usage statistics, and health status. Consolidates hosting, database, and SSL information.

        **Detailed Management (Legacy Tools):**
        - `accounts`: list Combell accounts with optional `asset_type` or `identifier` filters
        - `account`: get detailed account information
        - `linux_hostings`: list all Linux hosting accounts (consider using `hosting_overview` for richer information)
        - `linux_hosting`: get detailed hosting configuration
        - `database`: get MySQL database details
        - `dns_records`: get DNS records with filtering (consider using `domain_health` for health insights)

        Always prefer the most specific tool that answers the question. Returned payloads mirror the Combell API responses, so field names follow the snake_case format from the platform.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Server\Tool>>
     */
    protected array $tools = [DomainHealthTool::class, HostingOverviewTool::class, AccountsTool::class, AccountTool::class, LinuxHostingsTool::class, LinuxHostingTool::class, DatabaseTool::class, DnsRecordsTool::class];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
