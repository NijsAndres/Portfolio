<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use ResolvesStorageUrl;

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
     * Stored CV path for a locale, with English fallback. CVs live under the
     * per-locale keys cv_path_en / cv_path_nl; a missing Dutch CV falls back to
     * the English one, mirroring the rest of the multi-language content.
     * Returns null when neither is set.
     */
    public static function cvPath(string $locale): ?string
    {
        return static::get("cv_path_{$locale}") ?: static::get('cv_path_en');
    }

    /**
     * Resolve the CV for $locale (default: the active app locale) to a usable
     * URL, bridging Step 8 uploads and the legacy seeded file. A new upload
     * lives on the public disk and resolves to /storage/...; the seeded filename
     * falls back to public/assets/. Returns null when no CV is set.
     */
    public static function cvUrl(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return (new static)->resolveStorageUrl(static::cvPath($locale));
    }
}
