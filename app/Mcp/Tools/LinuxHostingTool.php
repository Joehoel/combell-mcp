<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class LinuxHostingTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get a specific Linux hosting from the Combell API by hosting identifier.

        This tool will give information on the following resources:
            - IP address
            - FTP credentials
            - SSH credentials
            - Subsites
            - PHP version
            - Names of all databases
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $domain = $request->get("domain");

        $hosting = $combell->linuxHostings()->getLinuxHosting($domain)->object();

        return Response::json($hosting);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            "domain" => JsonSchema::string()->required()->description("The domain name of the Linux hosting"),
        ];
    }
}
