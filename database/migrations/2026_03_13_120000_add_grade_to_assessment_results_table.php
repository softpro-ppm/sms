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
            if (!Schema::hasColumn('assessment_results', 'grade')) {
                $table->string('grade', 10)->nullable()->after('percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_results', function (Blueprint $table) {
            if (Schema::hasColumn('assessment_results', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }
};
