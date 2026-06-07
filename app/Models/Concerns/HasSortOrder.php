<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Shared behaviour for models with a NOT NULL `sort_order` column that the admin
 * drag-and-drop reorders. Centralises the ordering scope, the "lowest unused
 * value" default and the bulk reorder, all of which were copy-pasted across the
 * project/education/experience/filter controllers.
 */
trait HasSortOrder
{
    /** Canonical ordering: by sort_order, then id as a stable tie-breaker. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Lowest unused sort_order, so a new row slots in at the front. */
    public static function nextSortOrder(): int
    {
        $used = static::query()->pluck('sort_order')->all();
        $next = 0;
        while (in_array($next, $used, true)) {
            $next++;
        }

        return $next;
    }

    /**
     * Rewrite each row's sort_order to its position in the given id array,
     * inside a transaction (mirrors the admin drag-drop / API reorder).
     */
    public static function applyOrder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach ($ids as $position => $id) {
                static::query()->where('id', $id)->update(['sort_order' => $position]);
            }
        });
    }
}
