<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail for training partner wallet changes.
     */
    public function up(): void
    {
        Schema::create('partner_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_partner_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2); // positive = credit/recharge, negative = debit/deduction
            $table->string('type', 30); // student_approval | recharge | refund | adjustment
            $table->string('reference_type', 50)->nullable(); // Student, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('balance_after', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_wallet_transactions');
    }
};
