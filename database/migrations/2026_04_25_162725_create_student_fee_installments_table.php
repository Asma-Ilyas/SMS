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
        Schema::create('student_fee_installments', function (Blueprint $table) {
             $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_submission_type_id')->constrained()->onDelete('restrict');
            $table->integer('installment_number');  // 1, 2, 3...
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // unique constraint to avoid duplicate installments per student/fee type
            $table->unique(['student_id', 'fee_submission_type_id', 'installment_number'], 'unique_installment');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_installments');
    }
};
