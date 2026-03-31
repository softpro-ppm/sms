<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'course_id',
        'training_partner_id',
        'batch_name',
        'start_date',
        'end_date',
        'max_students',
        'course_fee',
        'registration_fee',
        'assessment_fee',
        'duration_days',
        'is_active',
        'is_legacy_batch',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_legacy_batch' => 'boolean',
        'course_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'assessment_fee' => 'decimal:2',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    /**
     * Batches a training partner may see: owned by them, or legacy/shared rows they have enrollments under.
     */
    public function scopeVisibleToTrainingPartner($query, ?int $trainingPartnerId)
    {
        if ($trainingPartnerId === null) {
            return $query;
        }

        return $query->where(function ($q) use ($trainingPartnerId) {
            $q->where('training_partner_id', $trainingPartnerId)
                ->orWhereHas('course', fn ($c) => $c->where('training_partner_id', $trainingPartnerId))
                ->orWhereHas('enrollments.student', fn ($s) => $s->where('training_partner_id', $trainingPartnerId));
        });
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /** Tuition fee for this batch (override), else from catalogue course. */
    public function getResolvedCourseFeeAttribute(): float
    {
        $this->loadMissing('course');

        return (float) ($this->course_fee ?? $this->course?->course_fee ?? 0);
    }

    public function getResolvedRegistrationFeeAttribute(): float
    {
        $this->loadMissing('course');

        return (float) ($this->registration_fee ?? $this->course?->registration_fee ?? 0);
    }

    public function getResolvedAssessmentFeeAttribute(): float
    {
        $this->loadMissing('course');

        return (float) ($this->assessment_fee ?? $this->course?->assessment_fee ?? 0);
    }

    public function getResolvedTotalFeeAttribute(): float
    {
        return $this->resolved_course_fee + $this->resolved_registration_fee + $this->resolved_assessment_fee;
    }

    public function getResolvedDurationDaysAttribute(): ?int
    {
        $this->loadMissing('course');
        if ($this->duration_days !== null) {
            return (int) $this->duration_days;
        }

        return $this->course?->duration_days !== null ? (int) $this->course->duration_days : null;
    }

    // Accessors
    public function getCurrentStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->max_students && $this->current_students_count >= $this->max_students;
    }

    public function getIsRunningAttribute(): bool
    {
        return $this->is_active && $this->start_date <= now() && $this->end_date >= now();
    }
}
