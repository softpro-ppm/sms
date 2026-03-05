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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('id');
            $table->json('options')->nullable()->after('option_d');
            $table->decimal('marks', 5, 2)->default(2.5)->after('correct_answer');
            $table->string('difficulty')->default('medium')->after('marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['subject_id', 'options', 'marks', 'difficulty']);
        });
    }
};