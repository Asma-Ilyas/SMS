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
        Schema::create('students', function (Blueprint $table) {
           $table->id();

            // Personal Details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->string('dob_in_words')->nullable();
            $table->string('religion')->nullable();
            $table->string('caste_subcaste')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('extra_note')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('birth_place')->nullable();

            // Previous School Details (including Category)
            $table->string('previous_school_name')->nullable();
            $table->string('previous_school_address')->nullable();
            $table->string('previous_class')->nullable();
            $table->string('passout_year')->nullable();
            $table->string('previous_category')->nullable(); // General, OBC, SC, ST, EWS

            // Admission Details (without medium, subject, activity)
            $table->date('admission_date');
            $table->string('student_type'); // New Admission, Transfer, etc.
            $table->foreignId('class_id')->constrained('classes')->onDelete('restrict');
            $table->string('section');
            $table->string('admission_number')->unique();
            $table->string('roll_number')->nullable();
            $table->string('profile_photo')->nullable();

            // Parent Details
            $table->string('father_name');
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('parent_id_proof')->nullable();
            $table->string('parent_signature')->nullable();

            // Concession
            $table->string('assigned_concession')->nullable();

            // Status & Suspension
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->date('suspension_start_date')->nullable();
            $table->date('suspension_end_date')->nullable();
            $table->text('suspension_message')->nullable();

            $table->timestamps();
        });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
