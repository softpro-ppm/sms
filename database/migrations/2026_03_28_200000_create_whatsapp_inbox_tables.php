<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 32)->unique()->comment('Digits only, typically country+number e.g. 919876543210');
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('training_partner_id')->nullable()->constrained('training_partners')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();

            $table->index(['training_partner_id', 'last_message_at']);
            $table->index('student_id');
            $table->index('last_message_at');
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->string('direction', 16)->comment('inbound, outbound');
            $table->string('meta_message_id')->nullable()->unique();
            $table->string('type', 32)->default('text')->comment('text, image, audio, video, document, unknown');
            $table->text('body')->nullable();
            $table->string('media_url', 2048)->nullable();
            $table->string('status', 24)->default('received')->comment('received, sent, delivered, read, failed');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['conversation_id', 'created_at']);
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
