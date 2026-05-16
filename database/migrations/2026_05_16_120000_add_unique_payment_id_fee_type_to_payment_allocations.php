<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prevent duplicate allocation rows for the same payment + fee type (idempotent approval).
     *
     * If this migration fails due to existing duplicates, dedupe payment_allocations manually first.
     */
    public function up(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropIndex(['payment_id', 'fee_type']);
        });

        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->unique(['payment_id', 'fee_type'], 'payment_allocations_payment_id_fee_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropUnique('payment_allocations_payment_id_fee_type_unique');
        });

        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->index(['payment_id', 'fee_type']);
        });
    }
};
