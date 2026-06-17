<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Resource controller for project filters.
 *
 * Filters are a separate, dashboard-managed concept linked to projects via the
 * filter_project pivot (many-to-many). The free-text Project `tags` are
 * unrelated and stay as the visual card badge.
 */
class FilterController extends Controller
{
    use ReordersEntities;

    // Filters no longer have a standalone index — they are listed on the
    // projects page (admin.projects.index). Create/edit/delete still live here.

    /** Persist a new drag-and-drop order (array of filter ids). */
    public function reorder(Request $request)
    {
        return $this->reorderUsing($request, Filter::class);
    }

    public function create()
    {
        return view('admin.filters.form', ['filter' => new Filter]);
    }

    public function store(Request $request)
    {
        Filter::create($this->validateData($request));

        return redirect()->route('admin.projects.index')->with('success', 'Filter created.');
    }

    public function edit(Filter $filter)
    {
        return view('admin.filters.form', compact('filter'));
    }

    public function update(Request $request, Filter $filter)
    {
        $filter->update($this->validateData($request, $filter));

        return redirect()->route('admin.projects.index')->with('success', 'Filter updated.');
    }

    public function destroy(Filter $filter)
    {
        // Pivot rows are removed automatically (cascadeOnDelete); projects stay.
        $filter->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Filter deleted.');
    }

    /**
     * Shared validation for store and update. The slug is derived from the name
     * and checked for uniqueness so duplicate filter names are rejected.
     */
    private function validateData(Request $request, ?Filter $filter = null): array
    {
        $request->merge(['slug' => Str::slug((string) $request->input('name.en'))]);

        $validated = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:50'],
            'name.nl' => ['nullable', 'string', 'max:50'],
            'slug' => [
                'required',
                'string',
                'max:50',
                Rule::unique('filters', 'slug')->ignore($filter?->id),
            ],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'slug.unique' => 'A filter with that name already exists.',
            'name.en.required' => 'The English filter name is required.',
        ]);

        // When left blank, slot the filter in at the front (NOT NULL column).
        $validated['sort_order'] ??= Filter::nextSortOrder();

        return $validated;
    }
}
