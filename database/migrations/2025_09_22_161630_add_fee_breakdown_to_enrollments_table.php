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
        Schema::table('enrollments', function (Blueprint $table) {
            // Check if columns exist before adding (idempotent)
            if (!Schema::hasColumn('enrollments', 'registration_fee')) {
                $table->decimal('registration_fee', 10, 2)->default(100.00)->after('outstanding_amount');
            }
            if (!Schema::hasColumn('enrollments', 'course_fee')) {
                $table->decimal('course_fee', 10, 2)->default(0)->after('registration_fee');
            }
            if (!Schema::hasColumn('enrollments', 'assessment_fee')) {
                $table->decimal('assessment_fee', 10, 2)->default(100.00)->after('course_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['registration_fee', 'course_fee', 'assessment_fee']);
        });
    }
};