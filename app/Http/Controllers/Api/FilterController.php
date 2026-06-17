<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Concerns\SerializesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    use ReordersEntities;
    use SerializesTranslations;

    public function index(): JsonResponse
    {
        return response()->json(
            $this->withTranslationsMany(Filter::withCount('projects')->ordered()->get())
        );
    }

    public function show(Filter $filter): JsonResponse
    {
        return response()->json($this->withTranslations($filter));
    }

    public function store(Request $request): JsonResponse
    {
        $filter = Filter::create($this->validateData($request));

        return response()->json($this->withTranslations($filter), 201);
    }

    public function update(Request $request, Filter $filter): JsonResponse
    {
        $filter->update($this->validateData($request, $filter));

        return response()->json($this->withTranslations($filter));
    }

    public function destroy(Filter $filter): JsonResponse
    {
        // Pivot rows are removed automatically (cascadeOnDelete); projects stay.
        $filter->delete();

        return response()->json(['deleted' => true]);
    }

    /** Persist a new order (array of filter ids; mirrors admin). */
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderUsing($request, Filter::class);
    }

    private function validateData(Request $request, ?Filter $filter = null): array
    {
        // Derive the slug from the English name and enforce uniqueness (mirrors admin).
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
        ]);

        $validated['sort_order'] ??= Filter::nextSortOrder();

        return $validated;
    }
}
