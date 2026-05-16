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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bank Transfer, Easypaisa, JazzCash, Cash
            $table->enum('type', ['bank', 'mobile_wallet', 'cash']);
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('set null');
            $table->string('account_number')->nullable(); // for mobile wallet: easypaisa number
            $table->string('qr_code_url')->nullable(); // for mobile wallets
            $table->string('instructions')->nullable(); // e.g., "Pay at any branch" or "Use easypaisa app"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
