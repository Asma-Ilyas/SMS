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
        Schema::create('elective_tracks', function (Blueprint $table) {
           $table->id();
    $table->foreignId('stream_id')->constrained()->onDelete('cascade');
    $table->string('name', 50); // "Biology", "Computer Science"
    $table->timestamps();

    $table->unique(['stream_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elective_tracks');
    }
};
