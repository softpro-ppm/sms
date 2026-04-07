<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('ams_sync_status', 24)->nullable()->index()->after('approved_at');
            $table->timestamp('ams_last_attempt_at')->nullable()->after('ams_sync_status');
            $table->unsignedInteger('ams_attempt_count')->default(0)->after('ams_last_attempt_at');
            $table->timestamp('ams_synced_at')->nullable()->after('ams_attempt_count');
            $table->text('ams_last_error')->nullable()->after('ams_synced_at');
            $table->string('ams_transaction_id', 64)->nullable()->after('ams_last_error');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'ams_sync_status',
                'ams_last_attempt_at',
                'ams_attempt_count',
                'ams_synced_at',
                'ams_last_error',
                'ams_transaction_id',
            ]);
        });
    }
};

