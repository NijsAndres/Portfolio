<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

/**
 * Resource controller for work experience entries.
 *
 * Routes (resource) and the auth middleware are registered in Step 5 under the
 * /admin prefix. The shared create/edit form view comes in Step 6.
 */
class ExperienceController extends Controller
{
    use ReordersEntities;

    public function index()
    {
        $experience = Experience::ordered()->get();

        return view('admin.experience.index', compact('experience'));
    }

    /** Persist a new drag-and-drop order (array of experience ids). */
    public function reorder(Request $request)
    {
        return $this->reorderUsing($request, Experience::class);
    }

    public function create()
    {
        return view('admin.experience.form', ['experience' => new Experience]);
    }

    public function store(Request $request)
    {
        Experience::create($this->validateData($request));

        return redirect()->route('admin.experience.index')->with('success', 'Experience entry created.');
    }

    public function edit(Experience $experience)
    {
        return view('admin.experience.form', compact('experience'));
    }

    public function update(Request $request, Experience $experience)
    {
        $experience->update($this->validateData($request));

        return redirect()->route('admin.experience.index')->with('success', 'Experience entry updated.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return redirect()->route('admin.experience.index')->with('success', 'Experience entry deleted.');
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

        // When left blank, slot the entry in at the front (NOT NULL column).
        $validated['sort_order'] ??= Experience::nextSortOrder();

        return $validated;
    }
}
