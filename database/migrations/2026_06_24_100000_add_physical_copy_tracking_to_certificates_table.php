<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->timestamp('physical_copy_issued_at')->nullable()->after('is_issued');
            $table->foreignId('physical_copy_issued_by')
                ->nullable()
                ->after('physical_copy_issued_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('physical_copy_notes')->nullable()->after('physical_copy_issued_by');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('physical_copy_issued_by');
            $table->dropColumn(['physical_copy_issued_at', 'physical_copy_notes']);
        });
    }
};
