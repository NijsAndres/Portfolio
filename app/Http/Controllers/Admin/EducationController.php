<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReordersEntities;
use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;

/**
 * Resource controller for education entries.
 *
 * Routes (resource) and the auth middleware are registered in Step 5 under the
 * /admin prefix. The shared create/edit form view comes in Step 6.
 */
class EducationController extends Controller
{
    use ReordersEntities;

    public function index()
    {
        $education = Education::ordered()->get();

        return view('admin.education.index', compact('education'));
    }

    /** Persist a new drag-and-drop order (array of education ids). */
    public function reorder(Request $request)
    {
        return $this->reorderUsing($request, Education::class);
    }

    public function create()
    {
        return view('admin.education.form', ['education' => new Education()]);
    }

    public function store(Request $request)
    {
        Education::create($this->validateData($request));

        return redirect()->route('admin.education.index')->with('success', 'Education entry created.');
    }

    public function edit(Education $education)
    {
        return view('admin.education.form', compact('education'));
    }

    public function update(Request $request, Education $education)
    {
        $education->update($this->validateData($request));

        return redirect()->route('admin.education.index')->with('success', 'Education entry updated.');
    }

    public function destroy(Education $education)
    {
        $education->delete();

        return redirect()->route('admin.education.index')->with('success', 'Education entry deleted.');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'institution' => ['required', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // When left blank, slot the entry in at the front (NOT NULL column).
        $validated['sort_order'] ??= Education::nextSortOrder();

        return $validated;
    }
}
