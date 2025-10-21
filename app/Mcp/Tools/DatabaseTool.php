<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class DatabaseTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get a specific database from the Combell API by database name. To know database names associated with a domain/hosting, use the LinuxHostingTool tool.

        This tool will give information on the following resources:
            - Database name
            - Database hostname
            - Database user count
            - Database max size (in MB)
            - Database actual size (in MB)
            - Account Identifier
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $databaseName = $request->get("database_name");

        $database = $combell->mySqlDatabases()->getMySqlDatabase($databaseName)->object();

        if (!$database) {
            return Response::error("Database not found for name: {$databaseName}");
        }

        return Response::json($database);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            "database_name" => JsonSchema::string()->required()->description("The name of the database"),
        ];
    }
}
