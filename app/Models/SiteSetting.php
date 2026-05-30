<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    // Simple key/value table — no timestamp columns.
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Read a setting value by key, returning $default when it is missing.
     * Lets callers do SiteSetting::get('cv_path') instead of hand-writing queries.
     */
    public static function get(string $key, $default = null)
    {
        $value = static::query()->where('key', $key)->value('value');

        return $value ?? $default;
    }

    /**
     * Create or update a setting in one call.
     */
    public static function set(string $key, $value): void
    {
        static::query()->updateOrInsert(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Resolve the stored cv_path to a usable URL, bridging Step 8 uploads and
     * the legacy seeded file. A new upload lives on the public disk and
     * resolves to /storage/...; the seeded filename falls back to
     * public/assets/. Returns null when no CV is set.
     */
    public static function cvUrl(): ?string
    {
        $path = static::get('cv_path');

        if (! $path) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('assets/'.$path);
    }
}
