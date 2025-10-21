<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AccountsTool;
use App\Mcp\Tools\AccountTool;
use App\Mcp\Tools\LinuxHostingsTool;
use App\Mcp\Tools\LinuxHostingTool;
use App\Mcp\Tools\DatabaseTool;
use Joehoel\Combell\Resource\Accounts;
use Laravel\Mcp\Server;

class CombellServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = "Combell Server";

    /**
     * The MCP server's version.
     */
    protected string $version = "0.0.1";

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Instructions describing how to use the server and its features.
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
