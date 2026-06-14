<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactInfo extends Model
{
    use HasTranslations;

    protected $table = 'contact_info';

    /** Per-locale JSON fields (spatie/laravel-translatable). */
    public array $translatable = ['intro_text'];

    /**
     * Only an updated_at column exists on this table. See HeroContent for the
     * rationale: timestamps stay on, created_at is disabled, updated_at is
     * managed by Eloquent and so is not mass-assignable.
     */
    const CREATED_AT = null;

    protected $fillable = [
        'email',
        'phone',
        'linkedin_url',
        'github_url',
        'intro_text',
    ];
}
