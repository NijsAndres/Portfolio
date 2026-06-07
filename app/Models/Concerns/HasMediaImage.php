<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared image handling for models that point at a library image via media_id
 * (Project, HeroContent). Prefers the linked media record; otherwise bridges
 * Step 8 uploads and legacy seeded assets through the nullable image_path
 * fallback. Consuming models define imageAltFallback() for their default alt.
 */
trait HasMediaImage
{
    use ResolvesStorageUrl;

    /** The library image this model points at. Preferred over legacy image_path. */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /** Resolve the image to a usable URL: linked media first, else image_path. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->media?->url ?? $this->resolveStorageUrl($this->image_path);
    }

    /** Alt text: the linked media's alt, falling back to the model's default. */
    public function getImageAltAttribute(): string
    {
        return $this->media?->alt ?: $this->imageAltFallback();
    }

    /** Per-model default alt text when no media alt is set. */
    abstract protected function imageAltFallback(): string;
}
