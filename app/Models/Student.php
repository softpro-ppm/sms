<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'aadhar_number',
        'full_name',
        'father_name',
        'credit_balance',
        'gender',
        'qualification',
        'email',
        'phone',
        'whatsapp_number',
        'date_of_birth',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'is_active',
        'approved_at'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
        'credit_balance' => 'decimal:2',
    ];

    // Relationships
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(StudentCreditTransaction::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    // Accessors
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getTotalOutstandingAmountAttribute(): float
    {
        return $this->enrollments()->sum('outstanding_amount');
    }
}
