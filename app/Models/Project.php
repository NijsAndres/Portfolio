<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Resolve image_path to a usable URL, bridging Step 8 uploads and the
     * legacy seeded assets. New uploads live on the public disk and resolve to
     * /storage/...; seeded paths (e.g. 'projects/x.webp') fall back to the
     * files shipped in public/assets/.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset('assets/'.$this->image_path);
    }
}
