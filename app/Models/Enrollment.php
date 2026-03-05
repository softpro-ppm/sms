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
        'assessment_fee'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'total_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'is_eligible_for_assessment' => 'boolean',
        'registration_fee' => 'decimal:2',
        'course_fee' => 'decimal:2',
        'assessment_fee' => 'decimal:2'
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
    public function getCourseAttribute(): Course
    {
        return $this->batch->course;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->outstanding_amount <= 0;
    }

    public function getCanTakeAssessmentAttribute(): bool
    {
        if (!$this->batch || !$this->batch->end_date) {
            return false;
        }

        $endDate = Carbon::parse($this->batch->end_date);
        $validUntil = $endDate->copy()->addYear();

        return $this->is_eligible_for_assessment &&
               $this->is_fully_paid &&
               now()->gte($endDate) &&
               now()->lte($validUntil);
    }
}
