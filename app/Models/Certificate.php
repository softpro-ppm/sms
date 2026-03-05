<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'batch_id',
        'enrollment_id',
        'assessment_result_id',
        'certificate_number',
        'issue_date',
        'certificate_content',
        'certificate_file_path',
        'is_issued'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_issued' => 'boolean'
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function assessmentResult(): BelongsTo
    {
        return $this->belongsTo(AssessmentResult::class);
    }

    // Accessors
    public function getCertificateUrlAttribute(): string
    {
        return $this->certificate_file_path ? asset('storage/' . $this->certificate_file_path) : '';
    }
}
