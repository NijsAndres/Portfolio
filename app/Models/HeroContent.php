<?php

namespace App\Models;

use App\Models\Concerns\HasMediaImage;
use Illuminate\Database\Eloquent\Model;

class HeroContent extends Model
{
    use HasMediaImage;

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

    /** Default alt for the hero image. */
    protected function imageAltFallback(): string
    {
        return 'Background image';
    }
}
