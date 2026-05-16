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
        Schema::create('student_transfers', function (Blueprint $table) {
             $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('transfer_type', ['outgoing', 'incoming']);
            $table->string('from_school')->nullable();   // for incoming transfers
            $table->string('to_school')->nullable();     // for outgoing transfers
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->string('document_path')->nullable();  // transfer certificate etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_transfers');
    }
};
