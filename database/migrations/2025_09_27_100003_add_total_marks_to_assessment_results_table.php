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
            // Add total_marks column if it doesn't exist
            if (!Schema::hasColumn('assessment_results', 'total_marks')) {
                $table->integer('total_marks')->default(0)->after('percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_results', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_results', 'total_marks')) {
                $table->dropColumn('total_marks');
            }
        });
    }
};
