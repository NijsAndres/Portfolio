<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns that become translatable (spatie/laravel-translatable). They now
     * store JSON keyed by locale — {"en": "...", "nl": "..."} — so existing
     * English scalars are wrapped under the "en" key. The slug on filters and
     * all non-text columns (urls, dates, tags, names of schools/companies) stay
     * as plain values and are intentionally absent here.
     */
    private array $map = [
        'hero_content' => ['headline', 'subheadline', 'tagline'],
        'about_content' => ['bio_text'],
        'projects' => ['title', 'description', 'body'],
        'education' => ['degree', 'period'],
        'experience' => ['role', 'period'],
        'contact_info' => ['intro_text'],
        'filters' => ['name'],
        'media' => ['alt', 'title', 'caption', 'description'],
    ];

    public function up(): void
    {
        // Widen the short string columns that will hold {en,nl} JSON to text.
        // The bio_text / description / body / intro_text columns are already text.
        Schema::table('hero_content', function (Blueprint $t) {
            $t->text('headline')->change();
            $t->text('subheadline')->nullable()->change();
            $t->text('tagline')->nullable()->change();
        });
        Schema::table('projects', fn (Blueprint $t) => $t->text('title')->change());
        Schema::table('education', function (Blueprint $t) {
            $t->text('degree')->nullable()->change();
            $t->text('period')->nullable()->change();
        });
        Schema::table('experience', function (Blueprint $t) {
            $t->text('role')->nullable()->change();
            $t->text('period')->nullable()->change();
        });
        Schema::table('filters', fn (Blueprint $t) => $t->text('name')->change());
        Schema::table('media', function (Blueprint $t) {
            $t->text('alt')->nullable()->change();
            $t->text('title')->nullable()->change();
            $t->text('caption')->nullable()->change();
        });

        // Wrap existing scalar values into {"en": value}.
        $this->transform(
            wrap: true,
            fn: fn ($value) => json_encode(['en' => $value], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );
    }

    public function down(): void
    {
        // Flatten {"en": ...} back to the plain English scalar first…
        $this->transform(
            wrap: false,
            fn: fn ($value) => json_decode((string) $value, true)['en'] ?? '',
        );

        // …then narrow the widened columns back to their original string type.
        Schema::table('hero_content', function (Blueprint $t) {
            $t->string('headline')->change();
            $t->string('subheadline')->nullable()->change();
            $t->string('tagline')->nullable()->change();
        });
        Schema::table('projects', fn (Blueprint $t) => $t->string('title')->change());
        Schema::table('education', function (Blueprint $t) {
            $t->string('degree')->nullable()->change();
            $t->string('period')->nullable()->change();
        });
        Schema::table('experience', function (Blueprint $t) {
            $t->string('role')->nullable()->change();
            $t->string('period')->nullable()->change();
        });
        Schema::table('filters', fn (Blueprint $t) => $t->string('name')->change());
        Schema::table('media', function (Blueprint $t) {
            $t->string('alt')->nullable()->change();
            $t->string('title')->nullable()->change();
            $t->string('caption')->nullable()->change();
        });
    }

    /**
     * Walk every translatable column on every row and convert its value.
     * Idempotent: when wrapping, rows already shaped as {"en": ...} are skipped;
     * when unwrapping, plain scalars are skipped.
     */
    private function transform(bool $wrap, callable $fn): void
    {
        foreach ($this->map as $table => $columns) {
            foreach (DB::table($table)->get() as $row) {
                $update = [];

                foreach ($columns as $col) {
                    $value = $row->$col;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    $decoded = json_decode((string) $value, true);
                    $isWrapped = is_array($decoded) && array_key_exists('en', $decoded);

                    // Skip rows that are already in the target shape.
                    if ($wrap === $isWrapped) {
                        continue;
                    }

                    $update[$col] = $fn($value);
                }

                if ($update) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            }
        }
    }
};
