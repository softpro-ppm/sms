<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('enrollments_student_id_batch_id_unique');
            $table->index(['student_id', 'batch_id'], 'enrollments_student_id_batch_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_student_id_batch_id_index');
            $table->unique(['student_id', 'batch_id'], 'enrollments_student_id_batch_id_unique');
        });
    }
};
