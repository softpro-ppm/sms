<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('title');
            $table->string('lesson_type', 32)->default('article')->comment('article, video_link');
            $table->longText('body')->nullable()->comment('HTML tutorial content');
            $table->string('video_url', 2048)->nullable();
            $table->unsignedSmallInteger('estimated_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_module_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_modules');
    }
};
