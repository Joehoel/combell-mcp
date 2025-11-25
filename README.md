# Combell MCP

This Laravel 12 application exposes a Model Context Protocol (MCP) server that communicates with Combell hosting services. It builds on top of the [Combell PHP SDK](https://github.com/Joehoel/combell-php-sdk) so editors and AI agents can work with Combell resources without handling credentials directly.

> [!WARNING]
> This project is under active development. In the future you will not need to clone and run this repository locally to use the Combell MCP server.

## Requirements

- PHP 8.3 with the `pcntl` and `pdo_mysql` extensions enabled
- Composer 2.6 or newer
- Node.js 20+ with npm or yarn
- MySQL 8 (or another compatible database) for local development

## Setup

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Copy `.env.example` to `.env`, then configure database access and Combell credentials:
   ```
   COMBELL_API_KEY=your-key
   COMBELL_API_SECRET=your-secret
   ```
3. Generate an application key and run database migrations:
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
4. Install Passport keys and default clients (needed for MCP OAuth):
   ```bash
   php artisan passport:install --uuids
   ```
5. When you need UI assets, compile them with:
   ```bash
   npm run dev
   ```

## MCP access in Cursor

1. Serve the application so MCP clients (Cursor, MCP Inspector, etc.) can reach the MCP endpoint:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```
2. In your MCP client (Cursor or MCP Inspector), add a new HTTP server with URL `http://127.0.0.1:8000/mcp`.
3. The server advertises OAuth (PKCE) per the MCP spec:
   - Authorization metadata: `http://127.0.0.1:8000/.well-known/oauth-authorization-server`
   - Resource metadata: `http://127.0.0.1:8000/.well-known/oauth-protected-resource`
4. The OAuth scope required is `mcp:use`.
5. Your Combell API token and secret are still required for tool calls. Supply them as headers:
   - `X-API-Key`: your Combell API token
   - `X-API-Secret`: your Combell API secret
   MCP Inspector supports custom headers; Cursor will reuse the headers you configure for the server.
6. After authorizing, the client can invoke the Combell MCP server while the Laravel app is running.

## Development workflow

- Run `php artisan test` to execute the Pest test suite.
- Run `vendor/bin/pint --dirty` before committing to fix styling issues.
- Start `php artisan queue:work` if you need to process queued jobs locally.
