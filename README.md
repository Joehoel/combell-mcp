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
4. When you need UI assets, compile them with:
   ```bash
   npm run dev
   ```

## MCP access in Cursor

1. Serve the application so Cursor can reach the MCP endpoint:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```
2. In Cursor, open **Settings → MCP Servers → Add HTTP Server**.
3. Choose a name (for example, `Combell`) and set the URL to `http://127.0.0.1:8000/mcp/combell`.
4. When prompted for authentication, pick **HTTP Basic** and enter your Combell API token as the username and your API secret as the password. (The server also accepts the `X-API-Key` / `X-API-Secret` headers for local testing.)
5. Save the configuration. Cursor can now invoke the Combell MCP server while the Laravel app is running.

## Development workflow

- Run `php artisan test` to execute the Pest test suite.
- Run `vendor/bin/pint --dirty` before committing to fix styling issues.
- Start `php artisan queue:work` if you need to process queued jobs locally.
