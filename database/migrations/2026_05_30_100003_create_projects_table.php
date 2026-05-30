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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable(); // one-line card summary
            $table->json('tags')->nullable();        // e.g. ["HTML","JS","PHP"]
            $table->string('url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('type')->nullable();      // modal badge: school / concept / internship
            $table->text('body')->nullable();        // rich modal detail content
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
