<?php

declare(strict_types=1);

use App\Mcp\Tools\DomainHealthTool;
use Joehoel\Combell\Combell;
use Joehoel\Combell\Requests\DnsRecords\GetRecords;
use Joehoel\Combell\Requests\Domains\GetDomains;
use Laravel\Mcp\Request;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('domain health tool analyzes domains correctly', function () {
    // Mock the Combell SDK responses
    $mockClient = new MockClient([
        GetDomains::class => MockResponse::make([
            [
                'domain_name' => 'example.com',
                'status' => 'active',
                'expiration_date' => now()->addDays(15)->toISOString(),
            ],
            [
                'domain_name' => 'expired.com',
                'status' => 'active',
                'expiration_date' => now()->subDays(5)->toISOString(),
            ],
        ], 200),
        GetRecords::class => MockResponse::make([
            ['type' => 'A', 'record_name' => '@', 'value' => '1.2.3.4'],
            ['type' => 'MX', 'record_name' => '@', 'value' => 'mail.example.com'],
            ['type' => 'NS', 'record_name' => '@', 'value' => 'ns1.combell.net'],
        ], 200),
    ]);

    $combell = Combell::fake($mockClient, 'key', 'secret');
    $tool = new DomainHealthTool();
    $request = new Request([]);

    $response = $tool->handle($request, $combell);
    $data = json_decode((string) $response->content(), true);

    expect($data)->toHaveKey('domains');
    expect($data)->toHaveKey('summary');

    expect($data['summary']['total_domains'])->toBe(2);
    expect($data['summary']['domains_with_issues'])->toBeGreaterThan(0); // One domain expired

    $expiredDomain = collect($data['domains'])->firstWhere('domain_name', 'expired.com');
    expect($expiredDomain['issues'])->toContain('Domain is expired');
    expect($expiredDomain['is_healthy'])->toBeFalse();
});

test('domain health tool handles missing DNS records', function () {
    $mockClient = new MockClient([
        GetDomains::class => MockResponse::make([
            [
                'domain_name' => 'nodns.com',
                'status' => 'active',
                'expiration_date' => now()->addDays(365)->toISOString(),
            ],
        ], 200),
        GetRecords::class => MockResponse::make([], 200), // No DNS records
    ]);

    $combell = Combell::fake($mockClient, 'key', 'secret');
    $tool = new DomainHealthTool();
    $request = new Request([]);

    $response = $tool->handle($request, $combell);
    $data = json_decode((string) $response->content(), true);

    $domain = $data['domains'][0];
    expect($domain['dns_health']['issues'])->toContain('Missing A records - domain may not resolve');
    expect($domain['dns_health']['issues'])->toContain('Missing MX records - email delivery may be affected');
    expect($domain['dns_health']['issues'])->toContain('Missing NS records - DNS delegation may be incorrect');
    expect($domain['is_healthy'])->toBeFalse();
});
