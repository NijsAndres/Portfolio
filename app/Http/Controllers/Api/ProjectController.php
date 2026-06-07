<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal JSON API for project CRUD (Step 12), consumed by the MCP server.
 * Guarded by the cms.token middleware in routes/api.php.
 *
 * Mirrors Admin\ProjectController's field rules, with one deliberate difference:
 * media_id is nullable here so a project can be created from chat ("add a
 * project called X") without first picking a library image — the model already
 * tolerates a null media_id (image_url falls back). Filter syncing is out of
 * scope (not part of the MCP tool set).
 */
class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Project::orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    public function store(Request $request): JsonResponse
    {
        $project = Project::create($this->validateData($request));

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $project->update($this->validateData($request));

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(['deleted' => true]);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
            'type' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // When left blank, default to the lowest unused sort_order (the column
        // is NOT NULL). Same scheme as Admin\ProjectController.
        if (($validated['sort_order'] ?? null) === null) {
            $used = Project::pluck('sort_order')->all();
            $next = 0;
            while (in_array($next, $used, true)) {
                $next++;
            }
            $validated['sort_order'] = $next;
        }

        return $validated;
    }
}
