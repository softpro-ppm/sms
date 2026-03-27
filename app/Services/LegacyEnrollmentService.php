<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Course;
use App\Models\TrainingPartner;

class LegacyEnrollmentService
{
    public static function hqPartner(): ?TrainingPartner
    {
        return TrainingPartner::query()->where('type', 'HQ')->orderBy('id')->first();
    }

    public static function legacyBatch(): ?Batch
    {
        return Batch::query()->where('is_legacy_batch', true)->orderBy('id')->first();
    }

    public static function legacyCourse(): ?Course
    {
        $batch = self::legacyBatch();

        return $batch?->course;
    }

    public static function legacyCourseId(): ?int
    {
        return self::legacyCourse()?->id;
    }

    public static function legacyBatchId(): ?int
    {
        return self::legacyBatch()?->id;
    }

    public static function isConfigured(): bool
    {
        return self::legacyBatch() !== null;
    }
}
