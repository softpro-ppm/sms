<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_member_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_member_attendances', 'check_in_verification_method')) {
                $table->string('check_in_verification_method')->nullable()->after('check_in_match_distance');
            }

            if (!Schema::hasColumn('staff_member_attendances', 'check_out_verification_method')) {
                $table->string('check_out_verification_method')->nullable()->after('check_out_match_distance');
            }

            if (!Schema::hasColumn('staff_member_attendances', 'check_in_verification_status')) {
                $table->string('check_in_verification_status')->nullable()->after('check_in_verification_method');
            }

            if (!Schema::hasColumn('staff_member_attendances', 'check_out_verification_status')) {
                $table->string('check_out_verification_status')->nullable()->after('check_out_verification_method');
            }

            if (!Schema::hasColumn('staff_member_attendances', 'check_in_fallback_reason')) {
                $table->string('check_in_fallback_reason')->nullable()->after('check_in_verification_status');
            }

            if (!Schema::hasColumn('staff_member_attendances', 'check_out_fallback_reason')) {
                $table->string('check_out_fallback_reason')->nullable()->after('check_out_verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff_member_attendances', function (Blueprint $table) {
            $columns = [
                'check_in_verification_method',
                'check_out_verification_method',
                'check_in_verification_status',
                'check_out_verification_status',
                'check_in_fallback_reason',
                'check_out_fallback_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('staff_member_attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
