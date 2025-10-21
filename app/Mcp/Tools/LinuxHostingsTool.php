<?php

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class LinuxHostingsTool extends Tool
{
    use PaginationTrait;

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get all Linux hostings from the Combell API with pagination support.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        // Get pagination parameters from request
        $pageSize = $request->get('page_size', 100);

        // Use pagination to get all Linux hostings
        $hostings = $this->paginate(
            fn($skip, $take) => $combell->linuxHostings()->getLinuxHostings($skip, $take),
            $pageSize
        );

        return Response::json([
            "linux_hostings" => $hostings,
            "total_count" => count($hostings)
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            "page_size" => JsonSchema::integer()->default(100)->description("Number of items per page (default: 100)"),
        ];
    }
}
