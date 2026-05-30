<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $table = 'contact_info';

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
