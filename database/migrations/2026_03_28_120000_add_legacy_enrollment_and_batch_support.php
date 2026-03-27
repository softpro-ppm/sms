<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\TrainingPartner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->boolean('is_legacy_batch')->default(false)->after('is_active');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('is_legacy')->default(false)->after('assessment_fee');
            $table->string('legacy_course_name')->nullable()->after('is_legacy');
            $table->date('legacy_start_date')->nullable()->after('legacy_course_name');
            $table->date('legacy_end_date')->nullable()->after('legacy_start_date');
            $table->foreignId('legacy_link_course_id')->nullable()->after('legacy_end_date')
                ->constrained('courses')->nullOnDelete();
        });

        $hq = TrainingPartner::query()->where('type', 'HQ')->orderBy('id')->first();

        if ($hq) {
            $course = Course::query()->firstOrCreate(
                [
                    'training_partner_id' => $hq->id,
                    'name' => 'Legacy (Archive)',
                ],
                [
                    'description' => 'Container course for HQ legacy / historical completions. Not used for regular batches.',
                    'course_fee' => 0,
                    'registration_fee' => 0,
                    'assessment_fee' => 0,
                    'duration_days' => 3650,
                    'is_active' => true,
                ]
            );

            Batch::query()->firstOrCreate(
                [
                    'training_partner_id' => $hq->id,
                    'is_legacy_batch' => true,
                ],
                [
                    'course_id' => $course->id,
                    'batch_name' => 'Legacy Batch',
                    'start_date' => '2010-01-01',
                    'end_date' => '2030-12-31',
                    'max_students' => null,
                    'is_active' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['legacy_link_course_id']);
            $table->dropColumn([
                'is_legacy',
                'legacy_course_name',
                'legacy_start_date',
                'legacy_end_date',
                'legacy_link_course_id',
            ]);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('is_legacy_batch');
        });
    }
};
