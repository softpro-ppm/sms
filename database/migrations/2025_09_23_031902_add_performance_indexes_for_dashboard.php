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
        Schema::table('payments', function (Blueprint $table) {
            // Add composite index for dashboard queries
            $table->index(['status', 'created_at'], 'payments_status_created_at_index');
            $table->index(['status', 'amount'], 'payments_status_amount_index');
        });

        Schema::table('students', function (Blueprint $table) {
            // Add index for status-based queries
            $table->index(['status', 'created_at'], 'students_status_created_at_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            // Add index for enrollment date queries
            $table->index(['enrollment_date'], 'enrollments_enrollment_date_index');
            $table->index(['status', 'enrollment_date'], 'enrollments_status_enrollment_date_index');
        });

        Schema::table('batches', function (Blueprint $table) {
            // Add index for active batch queries
            $table->index(['is_active', 'start_date', 'end_date'], 'batches_active_dates_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            // Add index for active course queries
            $table->index(['is_active'], 'courses_is_active_index');
        });

        Schema::table('assessment_results', function (Blueprint $table) {
            // Add index for recent assessment queries
            $table->index(['created_at'], 'assessment_results_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_at_index');
            $table->dropIndex('payments_status_amount_index');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_status_created_at_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_enrollment_date_index');
            $table->dropIndex('enrollments_status_enrollment_date_index');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex('batches_active_dates_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_is_active_index');
        });

        Schema::table('assessment_results', function (Blueprint $table) {
            $table->dropIndex('assessment_results_created_at_index');
        });
    }
};