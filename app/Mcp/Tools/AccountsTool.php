<?php

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
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
        Use this tool when you need an overview of Combell customer accounts, including each account's numeric `id`, public `identifier` (often a primary domain name), and associated `servicepack_id`. It walks the `/accounts` endpoint with automatic pagination so you do not have to manage `skip`/`take` yourself.

        **Returns**
        - `accounts`: array of account records as returned by the Combell API (`id`, `identifier`, `servicepack_id`, and any additional fields provided by the platform).
        - `total_count`: integer count of the collected account records.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        // Retrieve optional filters from the request
        $assetType = $request->get('asset_type');
        $identifier = $request->get('identifier');

        // Use pagination to get all accounts
        $accounts = $this->paginate(
            fn ($skip, $take) => $combell->accounts()->getAccounts($skip, $take, $assetType, $identifier)
        );

        return Response::json([
            'accounts' => $accounts,
            'total_count' => count($accounts),
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
            'asset_type' => JsonSchema::string()
                ->nullable()
                ->description("Optional Combell asset type filter (e.g. 'linuxhosting', 'domain')."),
            'identifier' => JsonSchema::string()
                ->nullable()
                ->description('Optional account identifier filter (usually the primary domain name).'),
        ];
    }
}
