#!/usr/bin/env node
/**
 * Portfolio CMS — MCP server (Step 12).
 *
 * Exposes the Laravel CMS (andresnijs.be) as MCP tools over stdio so the
 * portfolio can be managed from an MCP client (Claude Code / Claude Desktop).
 * Each tool is a thin wrapper around the token-protected /api/cms endpoints.
 *
 * Config (mcp-server/.env, or env vars passed by the client):
 *   CMS_API_URL    base URL incl. /api/cms, e.g. https://andresnijs.be/api/cms
 *   CMS_API_TOKEN  the Bearer token (must match Laravel's CMS_API_TOKEN)
 */
import path from "node:path";
import { fileURLToPath } from "node:url";
import dotenv from "dotenv";
import { z } from "zod";
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";

// Load .env from this folder regardless of the client's working directory.
// Client-supplied env vars win (dotenv does not override what is already set).
const __dirname = path.dirname(fileURLToPath(import.meta.url));
dotenv.config({ path: path.join(__dirname, ".env") });

const CMS_API_URL = process.env.CMS_API_URL;
const CMS_API_TOKEN = process.env.CMS_API_TOKEN;

if (!CMS_API_URL || !CMS_API_TOKEN) {
  console.error(
    "Missing CMS_API_URL or CMS_API_TOKEN. Set them in mcp-server/.env or pass them as env vars."
  );
  process.exit(1);
}

/**
 * Call the Laravel CMS API with the Bearer token. Returns parsed JSON, or
 * throws an Error carrying the status + response body on a non-2xx reply.
 */
async function cms(apiPath, { method = "GET", body } = {}) {
  const res = await fetch(`${CMS_API_URL}${apiPath}`, {
    method,
    headers: {
      Authorization: `Bearer ${CMS_API_TOKEN}`,
      Accept: "application/json",
      ...(body ? { "Content-Type": "application/json" } : {}),
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  const text = await res.text();
  if (!res.ok) {
    throw new Error(`CMS API ${method} ${apiPath} → ${res.status}: ${text}`);
  }
  return text ? JSON.parse(text) : null;
}

/** Wrap a value as an MCP text result (pretty-printed JSON). */
function ok(data) {
  return { content: [{ type: "text", text: JSON.stringify(data, null, 2) }] };
}

/** Run a CMS call and surface failures as an MCP error result, not a crash. */
async function run(fn) {
  try {
    return ok(await fn());
  } catch (err) {
    return { content: [{ type: "text", text: String(err.message ?? err) }], isError: true };
  }
}

/**
 * Strip undefined keys so partial updates only send the fields the caller set.
 */
function defined(obj) {
  return Object.fromEntries(Object.entries(obj).filter(([, v]) => v !== undefined));
}

const server = new McpServer({ name: "portfolio-cms", version: "1.0.0" });

/* ----------------------------------------------------------------- Hero --- */

server.registerTool(
  "get_hero",
  { title: "Get hero", description: "Read the hero section (headline, subheadline, tagline, skills, disciplines).", inputSchema: {} },
  () => run(() => cms("/hero"))
);

server.registerTool(
  "update_hero",
  {
    title: "Update hero",
    description: "Update the hero section. headline is required; other fields are optional. media_id sets the background image (see get_media).",
    inputSchema: {
      headline: z.string(),
      subheadline: z.string().optional(),
      tagline: z.string().optional(),
      skills: z.array(z.string()).optional(),
      disciplines: z.array(z.string()).optional(),
      media_id: z.number().int().nullable().optional(),
    },
  },
  (args) => run(() => cms("/hero", { method: "PUT", body: defined(args) }))
);

/* ---------------------------------------------------------------- About --- */

server.registerTool(
  "get_about",
  { title: "Get about", description: "Read the about section (bio_text, born_in, languages, date_of_birth).", inputSchema: {} },
  () => run(() => cms("/about"))
);

server.registerTool(
  "update_about",
  {
    title: "Update about",
    description: "Update the about section. All fields optional.",
    inputSchema: {
      bio_text: z.string().optional(),
      born_in: z.string().optional(),
      languages: z.string().optional(),
      date_of_birth: z.string().optional(),
    },
  },
  (args) => run(() => cms("/about", { method: "PUT", body: defined(args) }))
);

/* ----------------------------------------------------------------- Media --- */

server.registerTool(
  "get_media",
  {
    title: "Get media library",
    description: "List the media library (read-only). Use a returned id as media_id when setting a hero or project image.",
    inputSchema: {},
  },
  () => run(() => cms("/media"))
);

/* ------------------------------------------------------------- Projects --- */

// Shared field schema for create/update project (image + filters included).
const projectFields = {
  title: z.string(),
  description: z.string().optional(),
  tags: z.array(z.string()).optional(),
  url: z.string().url().optional(),
  media_id: z.number().int().nullable().optional(),
  type: z.string().optional(),
  body: z.string().optional(),
  sort_order: z.number().int().optional(),
  filter_ids: z.array(z.number().int()).optional(),
};

server.registerTool(
  "get_projects",
  { title: "Get projects", description: "List all portfolio projects (with their linked filters).", inputSchema: {} },
  () => run(() => cms("/projects"))
);

server.registerTool(
  "create_project",
  {
    title: "Create project",
    description:
      "Create a portfolio project. title is required. media_id sets the image (see get_media); filter_ids links filter tags (see get_filters); type/body/sort_order are optional.",
    inputSchema: projectFields,
  },
  (args) => run(() => cms("/projects", { method: "POST", body: defined(args) }))
);

server.registerTool(
  "update_project",
  {
    title: "Update project",
    description:
      "Update a project by id. title is required by the API; pass any other fields to change. filter_ids replaces the linked filters; omit it to leave them untouched.",
    inputSchema: { id: z.number().int(), ...projectFields },
  },
  ({ id, ...fields }) => run(() => cms(`/projects/${id}`, { method: "PUT", body: defined(fields) }))
);

server.registerTool(
  "delete_project",
  { title: "Delete project", description: "Delete a project by id.", inputSchema: { id: z.number().int() } },
  ({ id }) => run(() => cms(`/projects/${id}`, { method: "DELETE" }))
);

/* ------------------------------------------------------------ Education --- */

server.registerTool(
  "get_education",
  { title: "Get education", description: "List all education entries.", inputSchema: {} },
  () => run(() => cms("/education"))
);

const educationFields = {
  institution: z.string(),
  degree: z.string().optional(),
  period: z.string().optional(),
  sort_order: z.number().int().optional(),
};

server.registerTool(
  "create_education",
  {
    title: "Create education",
    description: "Create an education entry. institution is required. sort_order controls ordering (lower = first).",
    inputSchema: educationFields,
  },
  (args) => run(() => cms("/education", { method: "POST", body: defined(args) }))
);

server.registerTool(
  "update_education",
  {
    title: "Update education",
    description: "Update an education entry by id. institution is required by the API.",
    inputSchema: { id: z.number().int(), ...educationFields },
  },
  ({ id, ...fields }) => run(() => cms(`/education/${id}`, { method: "PUT", body: defined(fields) }))
);

server.registerTool(
  "delete_education",
  { title: "Delete education", description: "Delete an education entry by id.", inputSchema: { id: z.number().int() } },
  ({ id }) => run(() => cms(`/education/${id}`, { method: "DELETE" }))
);

/* ----------------------------------------------------------- Experience --- */

const experienceFields = {
  company: z.string(),
  role: z.string().optional(),
  period: z.string().optional(),
  sort_order: z.number().int().optional(),
};

server.registerTool(
  "get_experience",
  { title: "Get experience", description: "List all work-experience entries.", inputSchema: {} },
  () => run(() => cms("/experience"))
);

server.registerTool(
  "create_experience",
  {
    title: "Create experience",
    description: "Create a work-experience entry. company is required. sort_order controls ordering (lower = first).",
    inputSchema: experienceFields,
  },
  (args) => run(() => cms("/experience", { method: "POST", body: defined(args) }))
);

server.registerTool(
  "update_experience",
  {
    title: "Update experience",
    description: "Update a work-experience entry by id. company is required by the API.",
    inputSchema: { id: z.number().int(), ...experienceFields },
  },
  ({ id, ...fields }) => run(() => cms(`/experience/${id}`, { method: "PUT", body: defined(fields) }))
);

server.registerTool(
  "delete_experience",
  { title: "Delete experience", description: "Delete a work-experience entry by id.", inputSchema: { id: z.number().int() } },
  ({ id }) => run(() => cms(`/experience/${id}`, { method: "DELETE" }))
);

/* --------------------------------------------------------------- Filters --- */

server.registerTool(
  "get_filters",
  { title: "Get filters", description: "List the project filters (with project counts). Use an id as filter_ids on a project.", inputSchema: {} },
  () => run(() => cms("/filters"))
);

server.registerTool(
  "create_filter",
  {
    title: "Create filter",
    description: "Create a project filter. name is required; the slug is derived from it and must be unique.",
    inputSchema: { name: z.string(), sort_order: z.number().int().optional() },
  },
  (args) => run(() => cms("/filters", { method: "POST", body: defined(args) }))
);

server.registerTool(
  "update_filter",
  {
    title: "Update filter",
    description: "Update a project filter by id. name is required by the API; the slug is re-derived from it.",
    inputSchema: { id: z.number().int(), name: z.string(), sort_order: z.number().int().optional() },
  },
  ({ id, ...fields }) => run(() => cms(`/filters/${id}`, { method: "PUT", body: defined(fields) }))
);

server.registerTool(
  "delete_filter",
  { title: "Delete filter", description: "Delete a project filter by id (its project links are removed; projects stay).", inputSchema: { id: z.number().int() } },
  ({ id }) => run(() => cms(`/filters/${id}`, { method: "DELETE" }))
);

/* -------------------------------------------------------------- Contact --- */

server.registerTool(
  "get_contact",
  { title: "Get contact", description: "Read the contact details (email, phone, linkedin_url, github_url, intro_text).", inputSchema: {} },
  () => run(() => cms("/contact"))
);

server.registerTool(
  "update_contact",
  {
    title: "Update contact",
    description: "Update the contact details. All fields optional.",
    inputSchema: {
      email: z.string().email().optional(),
      phone: z.string().optional(),
      linkedin_url: z.string().url().optional(),
      github_url: z.string().url().optional(),
      intro_text: z.string().optional(),
    },
  },
  (args) => run(() => cms("/contact", { method: "PUT", body: defined(args) }))
);

/* ------------------------------------------------------------ Analytics --- */

server.registerTool(
  "get_analytics_summary",
  { title: "Get analytics summary", description: "Page views, CV downloads, contact clicks and top projects.", inputSchema: {} },
  () => run(() => cms("/analytics/summary"))
);

/* ------------------------------------------------------------------------- */

const transport = new StdioServerTransport();
await server.connect(transport);
console.error("portfolio-cms MCP server running on stdio");
