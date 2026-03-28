<?php

use App\Models\Assessment;
use App\Models\Batch;
use App\Services\LegacyEnrollmentService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $batch = Batch::query()->where('is_legacy_batch', true)->orderBy('id')->first();
        $course = $batch?->course;
        if (! $course) {
            return;
        }

        Assessment::query()->firstOrCreate(
            [
                'course_id' => $course->id,
                'title' => LegacyEnrollmentService::LEGACY_COMPLETION_ASSESSMENT_TITLE,
            ],
            [
                'description' => 'System use only: automatic A+ result for legacy batch students exempt from online exam.',
                'time_limit_minutes' => 1,
                'total_questions' => 25,
                'passing_percentage' => 0,
                'is_active' => false,
            ]
        );
    }

    public function down(): void
    {
        $batch = Batch::query()->where('is_legacy_batch', true)->orderBy('id')->first();
        $course = $batch?->course;
        if (! $course) {
            return;
        }

        Assessment::query()
            ->where('course_id', $course->id)
            ->where('title', LegacyEnrollmentService::LEGACY_COMPLETION_ASSESSMENT_TITLE)
            ->delete();
    }
};
