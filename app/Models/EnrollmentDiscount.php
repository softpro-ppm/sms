<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentDiscount extends Model
{
    protected $fillable = [
        'enrollment_id',
        'fee_type',
        'amount',
        'reason',
        'applied_by',
        'applied_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function getFeeTypeDisplayAttribute(): string
    {
        return match ($this->fee_type) {
            'registration' => 'Registration Fee',
            'course_fee' => 'Course Fee',
            'assessment_fee' => 'Assessment Fee',
            default => ucfirst((string) $this->fee_type),
        };
    }
}
