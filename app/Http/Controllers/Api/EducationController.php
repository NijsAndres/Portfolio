<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Internal JSON API for education CRUD (Step 12), consumed by the MCP server.
 * Guarded by the cms.token middleware in routes/api.php. Mirrors the field
 * rules in Admin\EducationController.
 */
class EducationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Education::orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function show(Education $education): JsonResponse
    {
        return response()->json($education);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        // Default a blank sort_order on create only (NOT NULL column); updates
        // never touch an unspecified sort_order.
        if (($data['sort_order'] ?? null) === null) {
            $data['sort_order'] = $this->nextSortOrder();
        }

        $education = Education::create($data);

        return response()->json($education, 201);
    }

    public function update(Request $request, Education $education): JsonResponse
    {
        $education->update($this->validateData($request));

        return response()->json($education);
    }

    public function destroy(Education $education): JsonResponse
    {
        $education->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Persist a new order. Accepts an array of education IDs in the desired
     * order and rewrites each row's sort_order to its index (mirrors admin).
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:education,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $position => $id) {
                Education::where('id', $id)->update(['sort_order' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $validated;
    }

    /** Lowest unused sort_order, so a new entry slots in at the front. */
    private function nextSortOrder(): int
    {
        $used = Education::pluck('sort_order')->all();
        $next = 0;
        while (in_array($next, $used, true)) {
            $next++;
        }

        return $next;
    }
}
