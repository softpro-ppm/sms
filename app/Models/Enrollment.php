<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Enrollment extends Model
{
    protected $fillable = [
        'enrollment_number',
        'student_id',
        'batch_id',
        'enrollment_date',
        'status',
        'total_fee',
        'paid_amount',
        'discount_amount',
        'outstanding_amount',
        'is_eligible_for_assessment',
        'registration_fee',
        'course_fee',
        'assessment_fee',
        'is_legacy',
        'legacy_course_name',
        'legacy_start_date',
        'legacy_end_date',
        'legacy_link_course_id',
        'last_accessed_course_lesson_id',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'is_eligible_for_assessment' => 'boolean',
        'registration_fee' => 'decimal:2',
        'course_fee' => 'decimal:2',
        'assessment_fee' => 'decimal:2',
        'is_legacy' => 'boolean',
        'legacy_start_date' => 'date',
        'legacy_end_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function legacyLinkCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'legacy_link_course_id');
    }

    public function lastAccessedLesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'last_accessed_course_lesson_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(EnrollmentDiscount::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(EnrollmentLessonCompletion::class);
    }

    /** @return Collection<int, int> */
    public function activeLessonIdsForCourse(Course $course): Collection
    {
        $lmsHost = $course->lmsHostCourse();

        return CourseLesson::query()
            ->where('is_active', true)
            ->whereHas('module', function ($q) use ($lmsHost) {
                $q->where('course_id', $lmsHost->id)->where('is_active', true);
            })
            ->pluck('id');
    }

    /**
     * Progress for online lessons, or null when this course has no active LMS lessons.
     *
     * @return array{total:int,completed:int,percent:float}|null
     */
    public function lmsProgressForCourse(?Course $course): ?array
    {
        if (! $course) {
            return null;
        }

        $ids = $this->activeLessonIdsForCourse($course);
        if ($ids->isEmpty()) {
            return null;
        }

        $done = $this->lessonCompletions()->whereIn('course_lesson_id', $ids)->count();
        $total = $ids->count();

        return [
            'total' => $total,
            'completed' => $done,
            'percent' => $total > 0 ? round(100 * $done / $total, 1) : 0.0,
        ];
    }

    public function isLmsFullyCompleteForCourse(?Course $course): bool
    {
        if (! $course) {
            return true;
        }

        $ids = $this->activeLessonIdsForCourse($course);
        if ($ids->isEmpty()) {
            return true;
        }

        $done = $this->lessonCompletions()->whereIn('course_lesson_id', $ids)->count();

        return $done >= $ids->count();
    }

    /**
     * Human-readable checklist for the Exams screen (and debugging).
     *
     * @return array<string, mixed>
     */
    public function getExamEligibilityChecklistAttribute(): array
    {
        $course = $this->course;
        $lms = $course ? $this->lmsProgressForCourse($course) : null;

        $batchEnded = false;
        $examWindowOpen = false;
        if ($this->batch?->end_date) {
            $end = Carbon::parse($this->batch->end_date)->startOfDay();
            $batchEnded = now()->startOfDay()->gte($end);
            $examWindowOpen = $batchEnded;
        }

        return [
            'institute_eligible' => (bool) $this->is_eligible_for_assessment,
            'fee_fully_paid' => (bool) $this->is_fully_paid,
            'batch_ended' => $batchEnded,
            'within_exam_window' => $examWindowOpen,
            'online_lessons_complete' => $lms === null ? true : ($lms['completed'] >= $lms['total']),
            'lms_progress' => $lms,
            'can_take' => (bool) $this->can_take_assessment,
            'is_legacy' => (bool) $this->is_legacy,
        ];
    }

    // Accessors
    /**
     * Course used for assessments / certificate FK when legacy links to catalogue; else batch course.
     */
    public function getCourseAttribute(): ?Course
    {
        if ($this->is_legacy && $this->legacy_link_course_id) {
            return $this->legacyLinkCourse;
        }

        return $this->batch?->course;
    }

    /** Display name: custom text for legacy; otherwise batch course name. */
    public function getDisplayCourseNameAttribute(): string
    {
        if ($this->is_legacy) {
            if ($this->legacy_course_name) {
                return $this->legacy_course_name;
            }

            return $this->legacyLinkCourse->name
                ?? $this->batch?->course?->name
                ?? 'Course';
        }

        return $this->batch?->course?->name ?? 'N/A';
    }

    public function getEffectiveStartDateAttribute(): ?Carbon
    {
        if ($this->is_legacy && $this->legacy_start_date) {
            return $this->legacy_start_date;
        }

        return $this->batch?->start_date;
    }

    public function getEffectiveEndDateAttribute(): ?Carbon
    {
        if ($this->is_legacy && $this->legacy_end_date) {
            return $this->legacy_end_date;
        }

        return $this->batch?->end_date;
    }

    /** Course id used to match Assessment / question bank (legacy uses optional link). */
    public function getAssessmentCourseIdAttribute(): ?int
    {
        if ($this->is_legacy) {
            return $this->legacy_link_course_id ? (int) $this->legacy_link_course_id : null;
        }

        return $this->batch?->course_id ? (int) $this->batch->course_id : null;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->outstanding_amount <= 0;
    }

    public function getNetPayableAmountAttribute(): float
    {
        return max(0, round((float) $this->total_fee - (float) $this->discount_amount, 2));
    }

    public function getCanTakeAssessmentAttribute(): bool
    {
        if (! $this->is_eligible_for_assessment || ! $this->is_fully_paid) {
            return false;
        }

        if ($this->is_legacy) {
            return false;
        }

        if (! $this->batch || ! $this->batch->end_date) {
            return false;
        }

        $endDate = Carbon::parse($this->batch->end_date)->startOfDay();

        if (! now()->startOfDay()->gte($endDate)) {
            return false;
        }

        $course = $this->course;
        if ($course && ! $this->isLmsFullyCompleteForCourse($course)) {
            return false;
        }

        return true;
    }
}
