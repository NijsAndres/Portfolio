<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DIY analytics (Step 11). Write-once event log — no updated_at. The public
     * /track endpoint inserts rows; the admin dashboard reads aggregate counts.
     * The (event, created_at) index backs the dashboard's per-event date ranges.
     */
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event')->index();   // cv_download, page_view, project_click, ...
            $table->string('meta')->nullable();  // optional context, e.g. the project title
            $table->timestamp('created_at')->nullable();

            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
