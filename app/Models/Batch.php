<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'course_id',
        'batch_name',
        'start_date',
        'end_date',
        'max_students',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
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
