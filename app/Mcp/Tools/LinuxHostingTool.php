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
        Use this tool when you need the full detail payload for a single Linux hosting. Provide the hosting's domain name to receive configuration data such as IP address information, FTP/SSH credentials, PHP version, defined subsites, and the list of linked MySQL database names.

        **Returns**
        The raw object returned by Combell's `/linuxhostings/{domain}` endpoint, which includes keys such as:
        - `domain_name`, `servicepack_id`, `ip`, `ip_type`
        - `ftp_enabled`, `ftp_username`
        - `ssh_host`, `ssh_username`
        - `php_version`, `sites[]`, `mysql_database_names[]`
        - `max_webspace_size`, `max_size`, `webspace_usage`, `actual_size`
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $domain = $request->get('domain');

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
            'domain' => JsonSchema::string()
                ->required()
                ->description("The domain name that identifies the Linux hosting (for example, 'example.com')."),
        ];
    }
}
