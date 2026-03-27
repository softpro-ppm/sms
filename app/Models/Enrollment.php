<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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

    public function getCanTakeAssessmentAttribute(): bool
    {
        if (! $this->is_eligible_for_assessment || ! $this->is_fully_paid) {
            return false;
        }

        if ($this->is_legacy) {
            return (bool) $this->legacy_link_course_id;
        }

        if (! $this->batch || ! $this->batch->end_date) {
            return false;
        }

        $endDate = Carbon::parse($this->batch->end_date);
        $validUntil = $endDate->copy()->addYear();

        return now()->gte($endDate) && now()->lte($validUntil);
    }
}
