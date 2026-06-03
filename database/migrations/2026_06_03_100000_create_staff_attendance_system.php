<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('face_reference_image_path')->nullable()->after('dismiss_catalog_onboarding');
            $table->timestamp('face_enrolled_at')->nullable()->after('face_reference_image_path');
        });

        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_partner_id')->nullable()->constrained('training_partners')->nullOnDelete();
            $table->date('attendance_date');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_in_image_path')->nullable();
            $table->string('check_out_image_path')->nullable();
            $table->string('check_in_ip', 45)->nullable();
            $table->string('check_out_ip', 45)->nullable();
            $table->text('check_in_user_agent')->nullable();
            $table->text('check_out_user_agent')->nullable();
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'attendance_date']);
            $table->index(['training_partner_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_reference_image_path', 'face_enrolled_at']);
        });
    }
};
