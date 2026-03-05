<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    protected $fillable = [
        'payment_id',
        'enrollment_id',
        'fee_type',
        'allocated_amount',
        'remaining_fee'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'remaining_fee' => 'decimal:2'
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Accessors
    public function getFeeTypeDisplayAttribute(): string
    {
        return match($this->fee_type) {
            'registration' => 'Registration Fee',
            'course_fee' => 'Course Fee',
            'assessment_fee' => 'Assessment Fee',
            default => ucfirst($this->fee_type)
        };
    }
}