<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Controller;
use App\Models\Filter;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;

/**
 * Resource controller for portfolio projects.
 *
 * Routes (resource) and the auth middleware are registered in Step 5 under the
 * /admin prefix. The shared create/edit form view comes in Step 6.
 */
class ProjectController extends Controller
{
    use ReordersEntities;

    public function index()
    {
        $projects = Project::ordered()->get();

        // Filters share this page (managed in a section below the projects).
        $filters = Filter::withCount('projects')->ordered()->get();

        return view('admin.projects.index', compact('projects', 'filters'));
    }

    /** Persist a new drag-and-drop order (array of project ids). */
    public function reorder(Request $request)
    {
        return $this->reorderUsing($request, Project::class);
    }

    public function create()
    {
        return view('admin.projects.form', [
            'project' => new Project,
            'filters' => $this->filterOptions(),
            'media' => Media::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $project = Project::create($data);
        $project->filters()->sync($this->validateFilters($request));

        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', [
            'project' => $project,
            'filters' => $this->filterOptions(),
            'media' => Media::latest()->get(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validateData($request);

        $project->update($data);
        $project->filters()->sync($this->validateFilters($request));

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    /**
     * Clone an existing project as a starting point for a new one. All fields and
     * the linked filters are copied; the media reference (media_id) is shared, as
     * the media library is built around reusing the same image across entities.
     *
     * replicate() keeps the original sort_order, so the copy (a higher id) sorts
     * directly below the original on the index (orderBy sort_order, then id).
     */
    public function duplicate(Project $project)
    {
        $copy = $project->replicate();
        $copy->title = $project->title.' (Copy)';
        $copy->save();
        $copy->filters()->sync($project->filters->pluck('id'));

        return redirect()->route('admin.projects.index')->with('success', 'Project duplicated.');
    }

    /**
     * Shared validation for store and update. The project image is chosen from
     * the media library (media_id); there is no direct file upload here anymore.
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.nl' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.nl' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'media_id' => ['required', 'integer', 'exists:media,id'],
            'type' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'array'],
            'body.en' => ['nullable', 'string'],
            'body.nl' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'media_id.required' => 'An image is required.',
            'media_id.exists' => 'The selected image is invalid.',
            'title.en.required' => 'The English title is required.',
        ]);

        // When left blank, slot the project in at the front (NOT NULL column).
        $validated['sort_order'] ??= Project::nextSortOrder();

        return $validated;
    }

    /**
     * Validate the linked filter ids (handled separately so they don't reach
     * Project::create/update, which has no filters column).
     */
    private function validateFilters(Request $request): array
    {
        $validated = $request->validate([
            'filters' => ['nullable', 'array'],
            'filters.*' => ['integer', 'exists:filters,id'],
        ]);

        return $validated['filters'] ?? [];
    }

    /**
     * Filters available to link on the project form.
     */
    private function filterOptions()
    {
        return Filter::ordered()->get();
    }
}
