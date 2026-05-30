<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $table = 'projects';

    // projects has full created_at/updated_at columns, so default timestamps apply.

    protected $fillable = [
        'title',
        'description',
        'tags',
        'url',
        'image_path',
        'type',
        'body',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Filters this project is linked to (many-to-many via filter_project).
     * Separate from the free-text `tags`, which remain the visual card badge.
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class);
    }
}
