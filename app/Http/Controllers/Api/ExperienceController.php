<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Concerns\SerializesTranslations;
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
    use ReordersEntities;
    use SerializesTranslations;

    public function index(): JsonResponse
    {
        return response()->json(
            $this->withTranslationsMany(Experience::ordered()->get())
        );
    }

    public function show(Experience $experience): JsonResponse
    {
        return response()->json($this->withTranslations($experience));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        // Default a blank sort_order on create only (NOT NULL column); updates
        // never touch an unspecified sort_order.
        $data['sort_order'] ??= Experience::nextSortOrder();

        $experience = Experience::create($data);

        return response()->json($this->withTranslations($experience), 201);
    }

    public function update(Request $request, Experience $experience): JsonResponse
    {
        $experience->update($this->validateData($request));

        return response()->json($this->withTranslations($experience));
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $experience->delete();

        return response()->json(['deleted' => true]);
    }

    /** Persist a new order (array of experience ids; mirrors admin). */
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderUsing($request, Experience::class);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'array'],
            'role.en' => ['nullable', 'string', 'max:255'],
            'role.nl' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'array'],
            'period.en' => ['nullable', 'string', 'max:255'],
            'period.nl' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $validated;
    }
}
