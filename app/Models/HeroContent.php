<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HeroContent extends Model
{
    protected $table = 'hero_content';

    /**
     * The hero_content table has only an updated_at column (no created_at).
     * Keep Eloquent's automatic timestamps enabled but disable created_at, so
     * updated_at is still touched automatically on every save. Because it is
     * managed by Eloquent it deliberately stays out of $fillable.
     */
    const CREATED_AT = null;

    protected $fillable = [
        'headline',
        'subheadline',
        'tagline',
        'skills',
        'disciplines',
        'image_path',
        'media_id',
    ];

    protected $casts = [
        'skills' => 'array',
        'disciplines' => 'array',
    ];

    /**
     * The library image the hero points at. Preferred over the legacy image_path.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Resolve the hero image to a usable URL. Prefers the linked media record;
     * otherwise bridges Step 8 uploads (public disk → /storage/...) and the
     * legacy seeded asset (falls back to public/assets/).
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
     * Alt text for the hero image — the linked media's alt, with a sensible
     * default so the frontend always has an alt attribute.
     */
    public function getImageAltAttribute(): string
    {
        return $this->media?->alt ?: 'Background image';
    }
}
