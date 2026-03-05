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
        Schema::table('certificates', function (Blueprint $table) {
            // Add new columns for enhanced certificate system
            if (!Schema::hasColumn('certificates', 'marksheet_file_path')) {
                $table->string('marksheet_file_path')->nullable()->after('certificate_file_path');
            }
            if (!Schema::hasColumn('certificates', 'grade')) {
                $table->enum('grade', ['A+', 'A', 'B', 'Fail'])->nullable()->after('marksheet_file_path');
            }
            if (!Schema::hasColumn('certificates', 'subject_wise_marks')) {
                $table->json('subject_wise_marks')->nullable()->after('grade');
            }
            if (!Schema::hasColumn('certificates', 'total_marks')) {
                $table->integer('total_marks')->default(0)->after('subject_wise_marks');
            }
            if (!Schema::hasColumn('certificates', 'correct_answers')) {
                $table->integer('correct_answers')->default(0)->after('total_marks');
            }
            if (!Schema::hasColumn('certificates', 'total_questions')) {
                $table->integer('total_questions')->default(20)->after('correct_answers');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $columns = ['marksheet_file_path', 'grade', 'subject_wise_marks', 'total_marks', 'correct_answers', 'total_questions'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('certificates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
