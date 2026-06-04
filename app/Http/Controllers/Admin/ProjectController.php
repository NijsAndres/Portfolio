<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // Filters share this page (managed in a section below the projects).
        $filters = Filter::withCount('projects')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.projects.index', compact('projects', 'filters'));
    }

    /**
     * Persist a new drag-and-drop order. Accepts an array of project IDs in the
     * desired order and rewrites each row's sort_order to its index.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:projects,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $position => $id) {
                Project::where('id', $id)->update(['sort_order' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
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
        $data = $this->validateData($request);

        // A new upload sets image_path; otherwise it stays null on create.
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create($data);
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
        $data = $this->validateData($request);

        // A new upload replaces the stored image; delete the previous file when
        // it lives on the public disk. With no new upload, keep image_path as-is.
        if ($request->hasFile('image')) {
            if ($project->image_path && Storage::disk('public')->exists($project->image_path)) {
                Storage::disk('public')->delete($project->image_path);
            }

            $data['image_path'] = $request->file('image')->store('projects', 'public');
        }

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
     * Clone an existing project as a starting point for a new one. All fields,
     * the linked filters, and the uploaded image are copied. The image is
     * duplicated into its own file so editing/deleting one project's image never
     * touches the other; legacy public/assets paths stay shared (consistent with
     * how the rest of this controller treats seeded images).
     *
     * replicate() keeps the original sort_order, so the copy (a higher id) sorts
     * directly below the original on the index (orderBy sort_order, then id).
     */
    public function duplicate(Project $project)
    {
        $copy = $project->replicate();
        $copy->title = $project->title.' (Copy)';

        if ($project->image_path && Storage::disk('public')->exists($project->image_path)) {
            $newPath = 'projects/'.Str::random(40).'.'.pathinfo($project->image_path, PATHINFO_EXTENSION);
            Storage::disk('public')->copy($project->image_path, $newPath);
            $copy->image_path = $newPath;
        }

        $copy->save();
        $copy->filters()->sync($project->filters->pluck('id'));

        return redirect()->route('admin.projects.index')->with('success', 'Project duplicated.');
    }

    /**
     * Shared validation for store and update.
     *
     * The uploaded `image` is validated here but handled in store/update (it is
     * stored on the public disk, with image_path holding the returned path).
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'type' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // `image` is the upload field, not a column — it is handled separately.
        unset($validated['image']);

        // When left blank, default to the lowest unused sort_order so the
        // project slots in at the front instead of failing the NOT NULL column.
        if ($validated['sort_order'] === null) {
            $used = Project::pluck('sort_order')->all();
            $next = 0;
            while (in_array($next, $used, true)) {
                $next++;
            }
            $validated['sort_order'] = $next;
        }

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
        return Filter::orderBy('sort_order')->orderBy('name')->get();
    }
}
