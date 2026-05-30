<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
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
    public function index()
    {
        $projects = Project::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.form', [
            'project' => new Project(),
            'filters' => $this->filterOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $project = Project::create($this->validateData($request));
        $project->filters()->sync($this->validateFilters($request));

        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', [
            'project' => $project,
            'filters' => $this->filterOptions(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $project->update($this->validateData($request));
        $project->filters()->sync($this->validateFilters($request));

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    /**
     * Shared validation for store and update.
     *
     * image_path is validated as a plain string for now; the real image upload
     * handling is added in Step 8.
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);
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
        return Filter::orderBy('sort_order')->orderBy('name')->get();
    }
}
