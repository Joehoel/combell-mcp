<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class LinuxHostingsTool extends Tool
{
    use PaginationTrait;

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Use this tool to list every Linux hosting environment that belongs to the authenticated Combell account. Each entry includes the hosting `domain_name` and `servicepack_id`, which you can pass into `LinuxHostingTool` or other tools for deeper inspection. Pagination is handled automatically.

        **Returns**
        - `linux_hostings`: array of Linux hosting summaries (`domain_name`, `servicepack_id`).
        - `total_count`: integer count of the collected hosting records.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $hostings = $this->paginate(
            fn (?int $skip, ?int $take): \Saloon\Http\Response => $combell->linuxHostings()->getLinuxHostings($skip, $take)
        );

        return Response::json([
            'linux_hostings' => $hostings,
            'total_count' => count($hostings),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
