<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-TP amount deducted from wallet when a student is approved.
     * HQ has no deduction. Set at create/edit/approve by Super Admin.
     */
    public function up(): void
    {
        Schema::table('training_partners', function (Blueprint $table) {
            $table->decimal('student_approval_deduction', 12, 2)->default(0)->after('wallet_balance');
        });
    }

    public function down(): void
    {
        Schema::table('training_partners', function (Blueprint $table) {
            $table->dropColumn('student_approval_deduction');
        });
    }
};
