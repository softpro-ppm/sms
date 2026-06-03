<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_wallet_transactions', function (Blueprint $table) {
            $table->string('collection_status', 20)->default('pending')->after('balance_after');
            $table->timestamp('collected_at')->nullable()->after('collection_status');
            $table->foreignId('collected_by')->nullable()->after('collected_at')->constrained('users')->nullOnDelete();
            $table->string('collection_notes')->nullable()->after('collected_by');
        });
    }

    public function down(): void
    {
        Schema::table('partner_wallet_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('collected_by');
            $table->dropColumn(['collection_status', 'collected_at', 'collection_notes']);
        });
    }
};
