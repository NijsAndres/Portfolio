<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Resource controller for work experience entries.
 *
 * Routes (resource) and the auth middleware are registered in Step 5 under the
 * /admin prefix. The shared create/edit form view comes in Step 6.
 */
class ExperienceController extends Controller
{
    public function index()
    {
        $experience = Experience::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.experience.index', compact('experience'));
    }

    /**
     * Persist a new drag-and-drop order. Accepts an array of experience IDs in
     * the desired order and rewrites each row's sort_order to its index.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:experience,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['order'] as $position => $id) {
                Experience::where('id', $id)->update(['sort_order' => $position]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function create()
    {
        return view('admin.experience.form', ['experience' => new Experience()]);
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
            'role' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        // When left blank, default to the lowest unused sort_order so the entry
        // slots in at the front instead of failing the NOT NULL column.
        if ($validated['sort_order'] === null) {
            $used = Experience::pluck('sort_order')->all();
            $next = 0;
            while (in_array($next, $used, true)) {
                $next++;
            }
            $validated['sort_order'] = $next;
        }

        return $validated;
    }
}
