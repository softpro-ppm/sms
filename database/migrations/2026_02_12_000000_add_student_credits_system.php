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
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('credit_balance', 10, 2)->default(0)->after('qualification');
        });

        Schema::create('student_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2); // positive = credit added, negative = credit used
            $table->string('type', 50); // enrollment_drop, enrollment_remove, enrollment_transfer, manual_adjustment
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('reference_enrollment_id')->nullable(); // For audit; enrollment may be deleted
            $table->timestamps();

            $table->index(['student_id', 'created_at']);
        });

        Schema::create('credit_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_credit_transaction_id')->constrained()->onDelete('cascade');
            $table->enum('fee_type', ['registration', 'course_fee', 'assessment_fee']);
            $table->decimal('allocated_amount', 10, 2);
            $table->decimal('remaining_fee', 10, 2);
            $table->timestamps();

            $table->index(['enrollment_id', 'fee_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_allocations');
        Schema::dropIfExists('student_credit_transactions');
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('credit_balance');
        });
    }
};
