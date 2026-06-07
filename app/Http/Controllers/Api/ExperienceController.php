<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal JSON API for work-experience CRUD (Step 12), consumed by the MCP
 * server. Guarded by the cms.token middleware in routes/api.php. Mirrors the
 * field rules in Admin\ExperienceController.
 */
class ExperienceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Experience::orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function show(Experience $experience): JsonResponse
    {
        return response()->json($experience);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        // Default a blank sort_order on create only (NOT NULL column); updates
        // never touch an unspecified sort_order.
        if (($data['sort_order'] ?? null) === null) {
            $data['sort_order'] = $this->nextSortOrder();
        }

        $experience = Experience::create($data);

        return response()->json($experience, 201);
    }

    public function update(Request $request, Experience $experience): JsonResponse
    {
        $experience->update($this->validateData($request));

        return response()->json($experience);
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $experience->delete();

        return response()->json(['deleted' => true]);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $validated;
    }

    /** Lowest unused sort_order, so a new entry slots in at the front. */
    private function nextSortOrder(): int
    {
        $used = Experience::pluck('sort_order')->all();
        $next = 0;
        while (in_array($next, $used, true)) {
            $next++;
        }

        return $next;
    }
}
