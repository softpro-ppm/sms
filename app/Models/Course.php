<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'course_fee',
        'registration_fee',
        'assessment_fee',
        'duration_days',
        'is_active'
    ];

    protected $casts = [
        'course_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'assessment_fee' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(Enrollment::class, Batch::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // Accessors
    public function getTotalFeeAttribute(): float
    {
        return $this->course_fee + $this->registration_fee + $this->assessment_fee;
    }
}
