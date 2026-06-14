<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Concerns\SerializesTranslations;
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
    use ReordersEntities;
    use SerializesTranslations;

    public function index(): JsonResponse
    {
        return response()->json(
            $this->withTranslationsMany(Education::ordered()->get())
        );
    }

    public function show(Education $education): JsonResponse
    {
        return response()->json($this->withTranslations($education));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        // Default a blank sort_order on create only (NOT NULL column); updates
        // never touch an unspecified sort_order.
        $data['sort_order'] ??= Education::nextSortOrder();

        $education = Education::create($data);

        return response()->json($this->withTranslations($education), 201);
    }

    public function update(Request $request, Education $education): JsonResponse
    {
        $education->update($this->validateData($request));

        return response()->json($this->withTranslations($education));
    }

    public function destroy(Education $education): JsonResponse
    {
        $education->delete();

        return response()->json(['deleted' => true]);
    }

    /** Persist a new order (array of education ids; mirrors admin). */
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderUsing($request, Education::class);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['nullable', 'array'],
            'degree.en' => ['nullable', 'string', 'max:255'],
            'degree.nl' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'array'],
            'period.en' => ['nullable', 'string', 'max:255'],
            'period.nl' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $validated;
    }
}
