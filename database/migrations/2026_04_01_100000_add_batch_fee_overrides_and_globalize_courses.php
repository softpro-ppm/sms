<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('course_fee', 10, 2)->nullable()->after('max_students');
            $table->decimal('registration_fee', 10, 2)->nullable()->after('course_fee');
            $table->decimal('assessment_fee', 10, 2)->nullable()->after('registration_fee');
            $table->unsignedInteger('duration_days')->nullable()->after('assessment_fee');
        });

        // Central catalogue: course rows are platform-wide (super admin maintains them).
        DB::table('courses')->update(['training_partner_id' => null]);
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['course_fee', 'registration_fee', 'assessment_fee', 'duration_days']);
        });
    }
};
