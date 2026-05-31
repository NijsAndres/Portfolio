<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $casts = [
        'skills' => 'array',
        'disciplines' => 'array',
    ];

    /**
     * Resolve image_path to a usable URL, bridging Step 8 uploads and the
     * legacy seeded asset. A new upload lives on the public disk and resolves
     * to /storage/...; the seeded path falls back to public/assets/.
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
