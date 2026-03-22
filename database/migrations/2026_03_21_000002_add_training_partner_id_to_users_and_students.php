<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('training_partner_id')->nullable()->after('student_id')->constrained('training_partners')->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('training_partner_id')->nullable()->after('id')->constrained('training_partners')->nullOnDelete();
        });

        // Backfill: set all existing users (admin/reception) and students to HQ
        $hqId = DB::table('training_partners')->where('code', 'HQ')->value('id');
        if ($hqId) {
            DB::table('users')->whereIn('role', ['admin', 'reception'])->update(['training_partner_id' => $hqId]);
            DB::table('students')->update(['training_partner_id' => $hqId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['training_partner_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['training_partner_id']);
        });
    }
};
