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
            // Make batch_id nullable
            $table->foreignId('batch_id')->nullable()->change();
            
            // Make assessment_result_id nullable
            $table->foreignId('assessment_result_id')->nullable()->change();
            
            // Make certificate_number nullable
            $table->string('certificate_number')->nullable()->change();
            
            // Make issue_date nullable
            $table->date('issue_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable(false)->change();
            $table->foreignId('assessment_result_id')->nullable(false)->change();
            $table->string('certificate_number')->nullable(false)->change();
            $table->date('issue_date')->nullable(false)->change();
        });
    }
};