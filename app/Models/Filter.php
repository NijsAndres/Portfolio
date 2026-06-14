<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Filter extends Model
{
    use HasSortOrder, HasTranslations;

    protected $table = 'filters';

    /**
     * Per-locale JSON field (spatie/laravel-translatable). The slug stays a
     * single, language-independent value derived from the English name.
     */
    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Projects linked to this filter (many-to-many via filter_project).
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
