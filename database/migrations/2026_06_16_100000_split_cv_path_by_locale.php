<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Per-language CV (NL/EN). The single `cv_path` site setting becomes two keys —
 * `cv_path_en` and `cv_path_nl` — so an English and a Dutch CV can be uploaded
 * independently. The existing `cv_path` value is treated as English and moved
 * to `cv_path_en`; a missing `cv_path_nl` falls back to English at read time
 * (see SiteSetting::cvUrl), matching the rest of the multi-language design.
 *
 * Data-only and idempotent: re-running won't clobber an already-split EN value.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Promote the legacy single CV to the English slot (only if not already done).
        if (! DB::table('site_settings')->where('key', 'cv_path_en')->exists()) {
            $legacy = DB::table('site_settings')->where('key', 'cv_path')->value('value');

            if ($legacy !== null) {
                DB::table('site_settings')->updateOrInsert(
                    ['key' => 'cv_path_en'],
                    ['value' => $legacy],
                );
            }
        }

        DB::table('site_settings')->where('key', 'cv_path')->delete();
    }

    public function down(): void
    {
        // Collapse back to the single key, preferring the English CV.
        $en = DB::table('site_settings')->where('key', 'cv_path_en')->value('value');

        if ($en !== null) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'cv_path'],
                ['value' => $en],
            );
        }

        DB::table('site_settings')->whereIn('key', ['cv_path_en', 'cv_path_nl'])->delete();
    }
};
