# Portfolio CMS — andresnijs.be

A Laravel-backed, admin-editable rebuild of [andresnijs.be](https://andresnijs.be).
What was once a static site is now a bilingual (NL/EN) portfolio with a custom CMS,
a media library, DIY analytics, and a token-protected API that's also exposed as an
MCP server — so the whole site can be edited from an AI assistant.

## Features

- **Admin CMS** — edit every section of the site (hero, about, projects, education,
  experience, contact) from `/admin`, behind single-user auth (Laravel Breeze).
- **Dynamic project filters** — many-to-many filters managed in the admin, rendered as
  filter buttons on the frontend.
- **Media library** — a central, WordPress-style image library with a reusable picker.
- **Multi-language (NL/EN)** — English served at `/`, Dutch at `/nl`. DB content is
  translatable (Spatie), UI chrome uses Laravel lang files, with a per-language CV.
- **DIY analytics** — a lightweight event log (page views, CV downloads, project clicks,
  contact clicks) with a dashboard widget and a 30-day chart.
- **CMS API + MCP server** — a token-protected JSON API over the models, wrapped by a
  stdio MCP server so an AI assistant can read and edit content directly.

## Stack

- **Laravel 13** + **SQLite** (`database/database.sqlite`)
- **Blade** templates, **Tailwind** + **Vite**
- **Laravel Breeze** (Blade) for auth — single admin user, no public registration
- **spatie/laravel-translatable** for bilingual content
- Served locally via **Laravel Herd**

## CMS API & MCP server

The API exposes the CMS over `/api/cms/*`, guarded by a Bearer token. The
`mcp-server/` directory wraps it as a stdio MCP server registering tools for every
editable field — singletons, full CRUD + reorder for projects/education/experience/filters,
media, CV and an analytics summary — so an AI assistant can read and edit content directly.
