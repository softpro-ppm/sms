<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    protected $fillable = [
        'training_partner_id',
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

    /**
     * Whether this partner sees Super Admin catalog courses (training_partner_id null). HQ yes; STANDARD no.
     */
    public static function includePlatformCatalogForPartner(?int $trainingPartnerId): bool
    {
        if ($trainingPartnerId === null) {
            return true;
        }

        static $cache = [];

        if (!array_key_exists($trainingPartnerId, $cache)) {
            $cache[$trainingPartnerId] = TrainingPartner::query()
                ->whereKey($trainingPartnerId)
                ->value('type') === 'HQ';
        }

        return $cache[$trainingPartnerId];
    }

    /**
     * Catalogue listing: all training partners see the same platform courses (maintained by super admin).
     * Kept as a named scope for call-site clarity; no row filter is applied.
     */
    public function scopeVisibleToTrainingPartner($query, ?int $trainingPartnerId, ?bool $includePlatformCatalog = null)
    {
        return $query;
    }

    /**
     * Assessments / question banks: TP staff work against the shared catalogue (same visibility as listing).
     */
    public function scopeWritableByTrainingPartner($query, ?int $trainingPartnerId)
    {
        return $query;
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    // Relationships
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /** LMS-style modules (lessons) attached to this course catalogue entry */
    public function learningModules(): HasMany
    {
        return $this->hasMany(CourseModule::class, 'course_id')->orderBy('sort_order');
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
