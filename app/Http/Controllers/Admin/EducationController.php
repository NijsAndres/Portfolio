<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Resource controller for education entries.
 *
 * Routes (resource) and the auth middleware are registered in Step 5 under the
 * /admin prefix. The shared create/edit form view comes in Step 6.
 */
class EducationController extends Controller
{
    public function index()
    {
        $education = Education::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.education.index', compact('education'));
    }

    /**
     * Persist a new drag-and-drop order. Accepts an array of education IDs in
     * the desired order and rewrites each row's sort_order to its index.
     */
    public function reorder(Request $request)
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

        // When left blank, default to the lowest unused sort_order so the entry
        // slots in at the front instead of failing the NOT NULL column.
        if ($validated['sort_order'] === null) {
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
