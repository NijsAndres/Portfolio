<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    // Filters no longer have a standalone index — they are listed on the
    // projects page (admin.projects.index). Create/edit/delete still live here.

    /**
     * Persist a new drag-and-drop order. Accepts an array of filter IDs in the
     * desired order and rewrites each row's sort_order to its index.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:filters,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $position => $id) {
                Filter::where('id', $id)->update(['sort_order' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function create()
    {
        return view('admin.filters.form', ['filter' => new Filter()]);
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
        $request->merge(['slug' => Str::slug((string) $request->input('name'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => [
                'required',
                'string',
                'max:50',
                Rule::unique('filters', 'slug')->ignore($filter?->id),
            ],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'slug.unique' => 'A filter with that name already exists.',
        ]);

        return $validated;
    }
}
