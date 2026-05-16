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
        Schema::create('section_fields', function (Blueprint $table) {
                $table->id();
    $table->foreignId('section_type_id')->constrained()->cascadeOnDelete();

    $table->string('name');        // heading, image, subtitle
    $table->string('field_type');  // text, image, textarea, color, select

    $table->json('options')->nullable(); // for select type, JSON ['top-left','center', etc.]
    
    $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_fields');
    }
};
