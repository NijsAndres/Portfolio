<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
