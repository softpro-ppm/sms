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
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->index(['student_id', 'assessment_id']);
            $table->index(['student_id', 'completed_at']);
            $table->index('assessment_id');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['course_id', 'is_active']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['student_id', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'created_at']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index(['student_id', 'is_issued']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'assessment_id']);
            $table->dropIndex(['student_id', 'completed_at']);
            $table->dropIndex(['assessment_id']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'is_active']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'status']);
            $table->dropIndex(['student_id', 'created_at']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'is_issued']);
        });
    }
};