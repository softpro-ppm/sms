<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['enrollment_id', 'course_lesson_id'], 'elc_enr_lesson_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_lesson_completions');
    }
};
