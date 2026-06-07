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
 * tolerates a null media_id (image_url falls back). Linked filters can be set
 * via the optional filter_ids array (synced, like the admin form).
 */
class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Project::with('filters')->orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project->load('filters'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        // On create only, default a blank sort_order to the lowest unused value
        // (NOT NULL column). On update we never touch an unspecified sort_order.
        if (($data['sort_order'] ?? null) === null) {
            $data['sort_order'] = $this->nextSortOrder();
        }

        $project = Project::create($data);
        $this->syncFilters($request, $project);

        return response()->json($project->load('filters'), 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $project->update($this->validateData($request));
        $this->syncFilters($request, $project);

        return response()->json($project->load('filters'));
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

        return $validated;
    }

    /** Lowest unused sort_order, so a new project slots in at the front. */
    private function nextSortOrder(): int
    {
        $used = Project::pluck('sort_order')->all();
        $next = 0;
        while (in_array($next, $used, true)) {
            $next++;
        }

        return $next;
    }

    /**
     * Sync the project's linked filters when filter_ids is present in the
     * request. Omitting the key leaves existing links untouched; passing an
     * empty array clears them. Mirrors the admin form's sync().
     */
    private function syncFilters(Request $request, Project $project): void
    {
        if (! $request->has('filter_ids')) {
            return;
        }

        $validated = $request->validate([
            'filter_ids' => ['array'],
            'filter_ids.*' => ['integer', 'exists:filters,id'],
        ]);

        $project->filters()->sync($validated['filter_ids'] ?? []);
    }
}
