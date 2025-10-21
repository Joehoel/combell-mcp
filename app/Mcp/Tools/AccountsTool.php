<?php

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Log;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AccountsTool extends Tool
{
    use PaginationTrait;

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get the accounts from the Combell API with pagination support.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        // Get pagination parameters from request
        $pageSize = $request->get('page_size', 100);
        $assetType = $request->get('asset_type');
        $identifier = $request->get('identifier');

        // Use pagination to get all accounts
        $accounts = $this->paginate(
            fn($skip, $take) => $combell->accounts()->getAccounts($skip, $take, $assetType, $identifier),
            $pageSize
        );

        return Response::json([
            "accounts" => $accounts,
            "total_count" => count($accounts)
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
            "asset_type" => JsonSchema::string()->nullable()->description("Filter by asset type"),
            "identifier" => JsonSchema::string()->nullable()->description("Filter by identifier"),
        ];
    }
}
