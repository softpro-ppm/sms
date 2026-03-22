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
        Schema::create('training_partners', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('STANDARD'); // HQ | STANDARD
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('logo_path')->nullable();
            $table->string('district')->nullable();
            $table->string('mandal')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->decimal('wallet_balance', 12, 2)->default(0);
            $table->string('status', 20)->default('active'); // active | suspended | pending
            $table->timestamps();
        });

        // Seed Softpro HQ (current production = this)
        DB::table('training_partners')->insert([
            'type' => 'HQ',
            'name' => 'Softpro Skill Solutions',
            'code' => 'HQ',
            'status' => 'active',
            'wallet_balance' => 999999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_partners');
    }
};
