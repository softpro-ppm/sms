<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('status')->default('sent');
            $table->string('dedupe_key')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->date('sent_on')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'type']);
            $table->index(['type', 'sent_on']);
            $table->unique(['student_id', 'dedupe_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_push_notification_logs');
    }
};
