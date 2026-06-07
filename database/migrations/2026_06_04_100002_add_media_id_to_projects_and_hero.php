<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link projects and the hero to a media library record. Nullable and set to
     * null on delete; the legacy image_path columns stay in place as a fallback.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->after('image_path')
                ->constrained('media')->nullOnDelete();
        });

        Schema::table('hero_content', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->after('image_path')
                ->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_id');
        });

        Schema::table('hero_content', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_id');
        });
    }
};
