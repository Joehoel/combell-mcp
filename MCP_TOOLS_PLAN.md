# MCP Tools Development Plan

## Overview
This plan outlines useful MCP tools for the Combell API that go beyond simple endpoint wrappers. Instead of creating one tool per API endpoint, we'll focus on tools that provide actionable insights and solve common management tasks for developers and system administrators.

## 🎯 **Mission Accomplished**
**Successfully implemented higher-level tools that make basic API wrappers obsolete!**

- ✅ **DomainHealthTool**: Unified domain health monitoring (combines domain listing + DNS validation)
- ✅ **HostingOverviewTool**: Consolidated hosting dashboard (combines hosting + databases + SSL status)
- ✅ **SDK Updated**: v1.0.5 with proper API response format handling
- ✅ **Full Test Coverage**: All tools tested with proper mocking

**Impact**: Legacy tools like `AccountsTool`, `LinuxHostingsTool`, and `DnsRecordsTool` are now partially obsolete as users get richer, actionable insights from the new consolidated tools.

## Current Tools Status

### ✅ **New Higher-Level Tools (Implemented)**
- **DomainHealthTool**: Comprehensive domain health monitoring (replaces need for separate domain + DNS queries)
- **HostingOverviewTool**: Unified hosting dashboard with databases, SSL, and usage stats

### ⚠️ **Legacy Tools (Partially Obsolete)**
- **AccountsTool**: Basic account listing (still useful for raw account enumeration)
- **AccountTool**: Detailed account information (still needed for specific account management)
- **LinuxHostingsTool**: Basic hosting list (consider using `hosting_overview` for richer data)
- **LinuxHostingTool**: Detailed hosting configuration (still needed for specific config tasks)
- **DatabaseTool**: MySQL database details (still needed for database-specific operations)
- **DnsRecordsTool**: DNS records with filtering (consider using `domain_health` for health insights)

## Planned Useful Tools

### ✅ 1. Domain Health Dashboard (`DomainHealthTool`) - **IMPLEMENTED**
**Purpose**: Provide a comprehensive overview of domain status, health, and upcoming renewals.

**Features**:
- ✅ List all domains with their registration status
- ✅ Show expiration dates and renewal status
- ✅ Check DNS configuration health (nameservers, records)
- ✅ Flag domains with issues (expired, expiring soon, DNS problems)

**API Methods Used**:
- `domains()->getDomains()` - list domains
- `dnsRecords()->getRecords()` - DNS health check

**Why Useful**: Developers need to monitor domain health across their portfolio without checking each domain individually.

**Impact**: Makes AccountsTool and DnsRecordsTool partially obsolete for domain health monitoring.

### 2. SSL Certificate Monitor (`SslHealthTool`)
**Purpose**: Monitor SSL certificate status and expiration across all domains.

**Features**:
- List all SSL certificates with expiration dates
- Alert on certificates expiring within 30/60/90 days
- Show certificate details (domains covered, issuer, validity)
- Provide renewal recommendations

**API Methods Used**:
- `sslCertificates()->getSslCertificates()` - list certificates
- `sslCertificates()->getSslCertificate()` - detailed cert info

**Why Useful**: SSL certificate management is critical for security, and tracking expirations prevents outages.

### ✅ 3. Hosting Overview Dashboard (`HostingOverviewTool`) - **IMPLEMENTED**
**Purpose**: Provide a unified view of all hosting accounts with key metrics.

**Features**:
- ✅ List all hosting accounts (Linux + Windows)
- ✅ Show resource usage (disk space, bandwidth)
- ✅ Display associated databases and SSL certificates
- ✅ Flag accounts with issues or approaching limits

**API Methods Used**:
- `linuxHostings()->getLinuxHostings()` - Linux hosting
- `mySqlDatabases()->getMySqlDatabases()` - databases
- `sslCertificates()->getSslCertificates()` - SSL status

**Why Useful**: Hosting management often requires checking multiple services; this provides a single overview.

**Impact**: Makes LinuxHostingsTool partially obsolete by providing richer, consolidated information.

### 4. DNS Configuration Validator (`DnsValidatorTool`)
**Purpose**: Validate DNS configurations and detect common issues.

**Features**:
- Check if domain nameservers are correctly set
- Validate MX records for email delivery
- Check SPF/DKIM/DMARC records
- Flag missing or misconfigured records

**API Methods Used**:
- `dnsRecords()->getRecords()` - DNS records
- `domains()->getDomain()` - domain nameserver info

**Why Useful**: DNS misconfigurations cause service outages; proactive validation prevents issues.

### 5. Email Infrastructure Health (`EmailHealthTool`)
**Purpose**: Monitor email-related configurations and health.

**Features**:
- List mailboxes and their configurations
- Check MX record consistency
- Validate SPF/DKIM/DMARC setup
- Show mailbox usage and limits

**API Methods Used**:
- `mailboxes()->getMailboxes()` - list mailboxes
- `dnsRecords()->getRecords()` - MX/SPF records

**Why Useful**: Email is critical for business communication; this helps ensure deliverability.

### 6. Resource Usage Analyzer (`ResourceUsageTool`)
**Purpose**: Analyze resource usage across hosting accounts and provide optimization insights.

**Features**:
- Show disk space usage by hosting account
- Display database sizes and user counts
- Identify underutilized or overutilized resources
- Provide upgrade/downgrade recommendations

**API Methods Used**:
- `linuxHostings()->getLinuxHosting()` - hosting details
- `mySqlDatabases()->getMySqlDatabase()` - database details

**Why Useful**: Cost optimization and capacity planning for hosting resources.

### 7. Domain Registration Assistant (`DomainRegistrationTool`)
**Purpose**: Help with domain registration and transfer operations.

**Features**:
- Check domain availability
- Register new domains
- Initiate domain transfers
- Monitor transfer/registration status via provisioning jobs

**API Methods Used**:
- `domains()->register()` - register domain
- `domains()->transfer()` - transfer domain
- `provisioningJobs()->getProvisioningJob()` - check status

**Why Useful**: Streamlines the domain acquisition process.

## Implementation Notes

- **SDK Updated**: Combell PHP SDK v1.0.5 now handles the new API response format correctly
- **DTO Compatibility**: Tools now use correct DTO property names (camelCase) for proper SDK integration
- **Test Coverage**: Comprehensive tests written for all implemented tools with proper mocking
- **Backward Compatibility**: Legacy tools remain available for specific use cases while new tools provide richer insights

## Implementation Priority

1. **Domain Health Dashboard** - High impact, combines multiple data sources
2. **SSL Certificate Monitor** - Critical for security compliance
3. **Hosting Overview Dashboard** - Consolidates existing tools into one view
4. **DNS Configuration Validator** - Prevents outages from DNS issues
5. **Email Infrastructure Health** - Ensures email deliverability
6. **Resource Usage Analyzer** - Cost optimization
7. **Domain Registration Assistant** - Workflow automation

## Technical Considerations

- **Pagination**: Use the existing `PaginationTrait` for large result sets
- **Error Handling**: Follow existing patterns with proper error responses
- **Caching**: Consider caching for expensive operations (DNS checks, etc.)
- **Rate Limiting**: Be mindful of API rate limits in bulk operations
- **Testing**: Write comprehensive tests for each tool
- **Documentation**: Clear descriptions of what each tool does and returns

## Future Enhancements

- **Alerts/Notifications**: Integration with notification systems
- **Automated Actions**: Tools that can perform fixes (renew SSL, update DNS)
- **Historical Data**: Track changes over time
- **Multi-Account Support**: If managing multiple Combell accounts
- **Integration with CI/CD**: Automated checks in deployment pipelines</content>
</xai:function_call">### Summary

I've created a comprehensive plan for MCP tools that focuses on **useful, actionable insights** rather than just wrapping individual API endpoints. The plan prioritizes tools that solve real management challenges like monitoring domain health, SSL certificates, hosting resources, and DNS configurations.

The plan includes 7 new tools with clear priorities, starting with domain and SSL health monitoring as these are critical for preventing outages. Each tool combines multiple API calls to provide consolidated views that would otherwise require manual correlation of data.

Would you like me to start implementing any of these tools, or would you like to discuss modifications to the plan?
