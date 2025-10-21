<?php

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AccountTool extends Tool
{
    use PaginationTrait;

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Use this tool when you know an account's public identifier (typically the primary domain name) and need the corresponding Combell account record. It enumerates `/accounts` until a match is found and returns the raw account payload (fields such as `id`, `identifier`, and `servicepack_id`).
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $domain = $request->get('domain');

        // Use pagination to search through all accounts
        $accounts = $this->paginate(
            fn ($skip, $take) => $combell->accounts()->getAccounts($skip, $take)
        );

        // Find the account by identifier
        $account = collect($accounts)->firstWhere('identifier', $domain);

        if (! $account) {
            return Response::error("Account not found for domain: {$domain}");
        }

        return Response::json($account);
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
                ->description('The Combell account identifier you want to resolve (usually a domain name).'),
        ];
    }
}
