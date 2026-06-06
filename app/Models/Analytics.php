<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single tracked event (DIY analytics, Step 11). Rows are write-once, so there
 * is no updated_at; Eloquent manages only created_at. EVENTS is the canonical
 * allow-list, reused by the /track endpoint validator and the dashboard.
 */
class Analytics extends Model
{
    protected $table = 'analytics';

    public const UPDATED_AT = null;

    public const EVENTS = [
        'page_view',
        'cv_download',
        'project_click',
        'contact_email',
        'contact_linkedin',
        'contact_github',
    ];

    protected $fillable = [
        'event',
        'meta',
    ];
}
