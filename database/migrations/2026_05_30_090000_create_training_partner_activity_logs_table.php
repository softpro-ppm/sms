<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_partner_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50);
            $table->string('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['training_partner_id', 'occurred_at'], 'tp_activity_tp_occurred_idx');
            $table->index(['training_partner_id', 'type'], 'tp_activity_tp_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_partner_activity_logs');
    }
};
