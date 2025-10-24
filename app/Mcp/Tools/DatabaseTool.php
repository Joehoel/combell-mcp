<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class DatabaseTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Use this tool when you know a MySQL database name and need its current configuration. You will receive the raw payload from `/mysqldatabases/{name}`, which is helpful after discovering databases via `LinuxHostingTool`.

        **Returns**
        A JSON object containing keys such as:
        - `name`, `hostname`
        - `user_count`
        - `max_size`, `actual_size`
        - `account_id`
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $databaseName = $request->get('database_name');

        $database = $combell->mySqlDatabases()->getMySqlDatabase($databaseName)->object();

        if (! $database) {
            return Response::error("Database not found for name: {$databaseName}");
        }

        return Response::json($database);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'database_name' => JsonSchema::string()
                ->required()
                ->description('The exact MySQL database name to inspect.'),
        ];
    }
}
