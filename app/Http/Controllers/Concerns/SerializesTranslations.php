<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Serializes translatable models for the JSON API (Step 12 / MCP) so every
 * translatable field comes back as its full {"en": …, "nl": …} object instead
 * of just the active locale. Spatie's default toArray() collapses each field to
 * the current locale, which would hide the other language from API/MCP clients
 * that need to read and edit both.
 */
trait SerializesTranslations
{
    protected function withTranslations(Model $model): array
    {
        return array_merge($model->toArray(), $model->getTranslations());
    }

    /** @param  iterable<Model>  $models */
    protected function withTranslationsMany(iterable $models): array
    {
        return collect($models)
            ->map(fn (Model $model) => $this->withTranslations($model))
            ->all();
    }
}
