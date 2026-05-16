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
        Schema::create('sections', function (Blueprint $table) {
           $table->id();
    $table->foreignId('page_id')->constrained()->cascadeOnDelete();
    $table->foreignId('section_type_id')->constrained()->cascadeOnDelete();
    
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);

    // Layout
    $table->string('position')->nullable();   // top, bottom, left, right
    $table->string('alignment')->nullable();  // left, center, right
    $table->json('style')->nullable();        // dynamic CSS

    // Dynamic color scheme
    $table->string('background_color')->nullable();
    $table->string('text_color')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
