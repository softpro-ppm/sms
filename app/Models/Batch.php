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
        'is_active',
        'is_legacy_batch',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_legacy_batch' => 'boolean',
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
