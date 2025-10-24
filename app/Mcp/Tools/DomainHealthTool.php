<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Exception;
use Illuminate\JsonSchema\JsonSchema;
use Joehoel\Combell\Combell;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

final class DomainHealthTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
    Get a comprehensive health overview of all domains in your Combell account. This tool provides actionable insights including:

    - Domain registration status and expiration dates
    - DNS health (nameserver configuration, basic record presence)
    - Issues that need attention (expiring domains, DNS problems)
    - Consolidated view that replaces the need for separate domain and DNS record queries

    **Returns**
    - `domains`: array of domain health objects with status, issues, and DNS information
    - `summary`: overview statistics (total domains, healthy domains, domains with issues)
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request, Combell $combell): Response
    {
        // Get all domains (paginated automatically if needed)
        $domains = $combell->domains()->getDomains()->dto();

        $domainHealth = [];
        $issuesCount = 0;
        $expiringSoonCount = 0;

        foreach ($domains as $domain) {
            $health = $this->analyzeDomainHealth($domain, $combell);

            if (! empty($health['issues'])) {
                $issuesCount++;
            }

            if ($health['expiring_soon']) {
                $expiringSoonCount++;
            }

            $domainHealth[] = $health;
        }

        return Response::json([
            'domains' => $domainHealth,
            'summary' => [
                'total_domains' => count($domains),
                'healthy_domains' => count($domains) - $issuesCount,
                'domains_with_issues' => $issuesCount,
                'domains_expiring_soon' => $expiringSoonCount,
            ],
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

    /**
     * Analyze the health of a single domain.
     */
    private function analyzeDomainHealth(object $domain, Combell $combell): array
    {
        $issues = [];
        $expiringSoon = false;

        // Check expiration
        $expirationDate = $domain->expirationDate ?? null;
        if ($expirationDate) {
            $daysUntilExpiry = now()->diffInDays(\Carbon\Carbon::parse($expirationDate), false);

            if ($daysUntilExpiry <= 0) {
                $issues[] = 'Domain is expired';
            } elseif ($daysUntilExpiry <= 30) {
                $issues[] = "Domain expires in {$daysUntilExpiry} days";
                $expiringSoon = true;
            } elseif ($daysUntilExpiry <= 90) {
                $expiringSoon = true;
            }
        }

        // Get DNS records for health analysis
        $dnsHealth = $this->analyzeDnsHealth($domain->domainName ?? '', $combell);
        $issues = array_merge($issues, $dnsHealth['issues']);

        return [
            'domain_name' => $domain->domainName ?? '',
            'status' => 'active', // Domain DTO doesn't have status, assume active
            'expiration_date' => $expirationDate,
            'days_until_expiry' => $expirationDate ? now()->diffInDays(\Carbon\Carbon::parse($expirationDate), false) : null,
            'expiring_soon' => $expiringSoon,
            'dns_health' => $dnsHealth,
            'issues' => $issues,
            'is_healthy' => $issues === [],
        ];
    }

    /**
     * Analyze DNS health for a domain.
     */
    private function analyzeDnsHealth(string $domainName, Combell $combell): array
    {
        $issues = [];

        try {
            $records = $combell->dnsRecords()->getRecords($domainName)->dto();

            // Check for basic DNS records
            $hasA = false;
            $hasMx = false;
            $hasNs = false;

            foreach ($records as $record) {
                $type = mb_strtolower($record->type ?? '');

                if ($type === 'a') {
                    $hasA = true;
                } elseif ($type === 'mx') {
                    $hasMx = true;
                } elseif ($type === 'ns') {
                    $hasNs = true;
                }
            }

            if (! $hasA) {
                $issues[] = 'Missing A records - domain may not resolve';
            }

            if (! $hasMx) {
                $issues[] = 'Missing MX records - email delivery may be affected';
            }

            if (! $hasNs) {
                $issues[] = 'Missing NS records - DNS delegation may be incorrect';
            }

            return [
                'record_count' => count($records),
                'has_basic_records' => $hasA && $hasMx && $hasNs,
                'issues' => $issues,
            ];

        } catch (Exception $e) {
            return [
                'record_count' => 0,
                'has_basic_records' => false,
                'issues' => ['Unable to check DNS records: '.$e->getMessage()],
            ];
        }
    }
}
