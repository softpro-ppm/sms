<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'reception', 'student', 'super_admin') DEFAULT 'student'");
        } elseif ($driver === 'sqlite') {
            // SQLite doesn't have ENUM - role is likely stored as string, no change needed
            // If the original migration used string type for sqlite, it already accepts any value
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // Revert any super_admin users to admin before dropping enum value
            DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'reception', 'student') DEFAULT 'student'");
        }
    }
};
