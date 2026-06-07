<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Shared drag-and-drop reorder action for the admin and API resource
 * controllers. Validates an `order` array of ids and rewrites each row's
 * sort_order to its position via the model's HasSortOrder::applyOrder().
 */
trait ReordersEntities
{
    /** @param  class-string<\App\Models\Concerns\HasSortOrder|\Illuminate\Database\Eloquent\Model>  $modelClass */
    protected function reorderUsing(Request $request, string $modelClass): JsonResponse
    {
        $table = (new $modelClass)->getTable();

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', "exists:{$table},id"],
        ]);

        $modelClass::applyOrder($validated['order']);

        return response()->json(['status' => 'ok']);
    }
}
