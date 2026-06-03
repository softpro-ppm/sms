<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_partners', function (Blueprint $table) {
            $table->decimal('attendance_latitude', 10, 7)->nullable()->after('student_approval_deduction');
            $table->decimal('attendance_longitude', 10, 7)->nullable()->after('attendance_latitude');
            $table->unsignedInteger('attendance_radius_meters')->nullable()->after('attendance_longitude');
        });

        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_partner_id')->nullable()->constrained('training_partners')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('staff_code')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('status')->default('pending');
            $table->json('face_descriptors')->nullable();
            $table->json('face_image_paths')->nullable();
            $table->timestamp('face_enrolled_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['training_partner_id', 'status']);
            $table->unique(['training_partner_id', 'staff_code']);
        });

        Schema::create('staff_member_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained('staff_members')->cascadeOnDelete();
            $table->foreignId('training_partner_id')->nullable()->constrained('training_partners')->nullOnDelete();
            $table->foreignId('kiosk_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_in_status')->nullable();
            $table->string('check_out_status')->nullable();
            $table->string('check_in_image_path')->nullable();
            $table->string('check_out_image_path')->nullable();
            $table->decimal('check_in_match_distance', 8, 5)->nullable();
            $table->decimal('check_out_match_distance', 8, 5)->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->unsignedInteger('check_in_distance_meters')->nullable();
            $table->unsignedInteger('check_out_distance_meters')->nullable();
            $table->unsignedInteger('check_in_accuracy_meters')->nullable();
            $table->unsignedInteger('check_out_accuracy_meters')->nullable();
            $table->string('check_in_ip', 45)->nullable();
            $table->string('check_out_ip', 45)->nullable();
            $table->text('check_in_user_agent')->nullable();
            $table->text('check_out_user_agent')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_member_id', 'attendance_date']);
            $table->index(['training_partner_id', 'attendance_date'], 'staff_member_att_tp_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_member_attendances');
        Schema::dropIfExists('staff_members');

        Schema::table('training_partners', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_latitude',
                'attendance_longitude',
                'attendance_radius_meters',
            ]);
        });
    }
};
