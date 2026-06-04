<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'media_id',
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
     * The library image this project points at (Step: media library). Preferred
     * over the legacy image_path, which stays as a fallback for un-migrated rows.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Resolve the project image to a usable URL. Prefers the linked media record;
     * otherwise bridges Step 8 uploads and legacy seeded assets via image_path:
     * uploads live on the public disk (/storage/...); seeded paths fall back to
     * the files shipped in public/assets/.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->media) {
            return $this->media->url;
        }

        if (! $this->image_path) {
            return null;
        }

        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset('assets/'.$this->image_path);
    }

    /**
     * Alt text for the project image — the linked media's alt, falling back to
     * the project title so frontend images always carry a sensible alt.
     */
    public function getImageAltAttribute(): string
    {
        return $this->media?->alt ?: $this->title;
    }
}
