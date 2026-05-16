<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prevent duplicate allocation rows for the same payment + fee type (idempotent approval).
     *
     * MySQL/MariaDB (error 1553): InnoDB may use the composite (payment_id, fee_type) index to
     * support the payment_id foreign key. That index cannot be dropped until another index on
     * payment_id exists — we add one first, then replace the composite with a unique constraint.
     *
     * If this migration fails due to existing duplicates, dedupe payment_allocations manually first.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)
            && ! Schema::hasIndex('payment_allocations', 'payment_allocations_payment_id_index')) {
            Schema::table('payment_allocations', function (Blueprint $table) {
                $table->index('payment_id', 'payment_allocations_payment_id_index');
            });
        }

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

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)
            && Schema::hasIndex('payment_allocations', 'payment_allocations_payment_id_index')) {
            Schema::table('payment_allocations', function (Blueprint $table) {
                $table->dropIndex('payment_allocations_payment_id_index');
            });
        }
    }
};
