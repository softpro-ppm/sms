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
        // Add full_name column
        Schema::table('students', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('aadhar_number');
        });
        
        // Combine existing names (SQLite compatible)
        DB::table('students')->update([
            'full_name' => DB::raw("first_name || ' ' || last_name")
        ]);
        
        // Drop the old columns
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back first_name and last_name columns
        Schema::table('students', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('aadhar_number');
            $table->string('last_name')->nullable()->after('first_name');
        });
        
        // Split full_name back (get first word as first_name, rest as last_name)
        DB::statement("UPDATE students SET 
            first_name = SUBSTR(full_name, 1, INSTR(full_name || ' ', ' ') - 1),
            last_name = TRIM(SUBSTR(full_name, INSTR(full_name || ' ', ' ') + 1))
        ");
        
        // Drop full_name column
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
};
