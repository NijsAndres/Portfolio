<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hero_content', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->string('subheadline')->nullable();
            $table->string('tagline')->nullable();
            $table->json('skills')->nullable();       // skills slider: HTML, SCSS, JavaScript, Laravel, PHP
            $table->json('disciplines')->nullable();  // hero trio: Design, Development, UX
            $table->string('image_path')->nullable(); // hero portrait image
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_content');
    }
};
