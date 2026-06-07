<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Internal JSON API for project filters (Step 12), consumed by the MCP server.
 * Guarded by the cms.token middleware. Filters link to projects via the
 * filter_project pivot; the slug is derived from the name and kept unique,
 * mirroring Admin\FilterController.
 */
class FilterController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Filter::withCount('projects')->orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function show(Filter $filter): JsonResponse
    {
        return response()->json($filter);
    }

    public function store(Request $request): JsonResponse
    {
        $filter = Filter::create($this->validateData($request));

        return response()->json($filter, 201);
    }

    public function update(Request $request, Filter $filter): JsonResponse
    {
        $filter->update($this->validateData($request, $filter));

        return response()->json($filter);
    }

    public function destroy(Filter $filter): JsonResponse
    {
        // Pivot rows are removed automatically (cascadeOnDelete); projects stay.
        $filter->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Persist a new order. Accepts an array of filter IDs in the desired order
     * and rewrites each row's sort_order to its index (mirrors admin).
     */
    public function reorder(Request $request): JsonResponse
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

    private function validateData(Request $request, ?Filter $filter = null): array
    {
        // Derive the slug from the name and enforce uniqueness (mirrors admin).
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

        if (($validated['sort_order'] ?? null) === null) {
            $used = Filter::pluck('sort_order')->all();
            $next = 0;
            while (in_array($next, $used, true)) {
                $next++;
            }
            $validated['sort_order'] = $next;
        }

        return $validated;
    }
}
