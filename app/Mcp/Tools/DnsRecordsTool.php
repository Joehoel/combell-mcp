<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\Traits\PaginationTrait;
use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class DnsRecordsTool extends Tool
{
    use PaginationTrait;

    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Use this tool to retrieve every DNS record for a given domain from Combell. You can optionally narrow the results by record `type`, `record_name`, or SRV `service`. The response mirrors the `/dnsrecords/{domain}` payload, making it easy to inspect record values, TTLs, priorities, and metadata for automation tasks.

        **Returns**
        - `records`: array of DNS record objects exactly as returned by the Combell API (fields such as `id`, `type`, `record_name`, `service`, `value`, `ttl`, `priority`, and more).
        - `total_count`: integer count of the collected DNS records.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        $domain = $request->get('domain');
        $type = $request->get('type');
        $recordName = $request->get('record_name');
        $service = $request->get('service');

        $records = $this->paginate(
            fn (?int $skip, ?int $take): \Saloon\Http\Response => $combell->dnsRecords()->getRecords($domain, $skip, $take, $type, $recordName, $service)
        );

        return Response::json([
            'records' => $records,
            'total_count' => count($records),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'domain' => JsonSchema::string()
                ->required()
                ->description('The domain name whose DNS records you want to retrieve.'),
            'type' => JsonSchema::string()
                ->nullable()
                ->description('Optional DNS record type filter (e.g. A, AAAA, CNAME, MX, TXT).'),
            'record_name' => JsonSchema::string()
                ->nullable()
                ->description('Optional record name (host) to filter results when type is provided.'),
            'service' => JsonSchema::string()
                ->nullable()
                ->description('Optional SRV service name filter (applies only to SRV records).'),
        ];
    }
}
