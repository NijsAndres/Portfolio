<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $education = Education::create($this->validateData($request));

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

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // When left blank, default to the lowest unused sort_order (NOT NULL).
        if (($validated['sort_order'] ?? null) === null) {
            $used = Education::pluck('sort_order')->all();
            $next = 0;
            while (in_array($next, $used, true)) {
                $next++;
            }
            $validated['sort_order'] = $next;
        }

        return $validated;
    }
}
