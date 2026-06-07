# Portfolio CMS — MCP server

Exposes the andresnijs.be Laravel CMS as MCP tools over **stdio** (Step 12 of the
CMS guide), so the portfolio can be managed from an MCP client such as **Claude
Code** or **Claude Desktop**. Each tool is a thin wrapper around the
token-protected `/api/cms` endpoints in the Laravel app.

> Note: stdio MCP servers are **local-only**. The Claude.ai web/mobile app does
> not connect to stdio servers (it needs a remote HTTP server). This one targets
> Claude Code / Claude Desktop.

## Setup

```bash
cd mcp-server
npm install
cp .env.example .env   # then edit .env
```

`.env`:

| Var | Value |
|---|---|
| `CMS_API_URL` | `https://andresnijs.be/api/cms` (prod) or `http://portfolio.test/api/cms` (local) |
| `CMS_API_TOKEN` | must match `CMS_API_TOKEN` in the Laravel `.env` |

The Laravel side must have the `/api/cms` routes deployed and the same
`CMS_API_TOKEN` set (run `php artisan config:cache` after changing it in prod).

## Run standalone (smoke test)

```bash
node index.js
# → "portfolio-cms MCP server running on stdio"  (Ctrl-C to stop)
```

## Connect to Claude Code

```bash
claude mcp add portfolio-cms -- node /Users/andresnijs/Herd/Portfolio/mcp-server/index.js
```

Then `/mcp` (or `claude mcp list`) shows it connected. Config is read from this
folder's `.env`. To override per-registration, pass env flags:

```bash
claude mcp add portfolio-cms \
  -e CMS_API_URL=https://andresnijs.be/api/cms \
  -e CMS_API_TOKEN=xxxxx \
  -- node /Users/andresnijs/Herd/Portfolio/mcp-server/index.js
```

## Connect to Claude Desktop

Add to `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "portfolio-cms": {
      "command": "node",
      "args": ["/Users/andresnijs/Herd/Portfolio/mcp-server/index.js"]
    }
  }
}
```

## Tools

`get_hero` · `update_hero` · `get_about` · `update_about` · `get_projects` ·
`create_project` · `update_project` · `delete_project` · `get_education` ·
`create_education` · `update_education` · `delete_education` · `get_contact` ·
`update_contact` · `get_analytics_summary`

(CV upload is intentionally not a tool — file uploads over stdio aren't practical;
the `POST /api/cms/cv` endpoint still exists for the admin UI.)
