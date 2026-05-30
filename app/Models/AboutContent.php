<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $table = 'about_content';

    /**
     * Only an updated_at column exists on this table. See HeroContent for the
     * rationale: timestamps stay on, created_at is disabled, updated_at is
     * managed by Eloquent and so is not mass-assignable.
     */
    const CREATED_AT = null;

    protected $fillable = [
        'bio_text',
        'born_in',
        'languages',
        'date_of_birth',
    ];
}
