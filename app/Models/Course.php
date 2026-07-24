<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    /** @var array<int, self> */
    private static array $lmsHostCourseCache = [];

    /** @var array<int, self> */
    private static array $assessmentHostCourseCache = [];

    /** @var array<int, bool> */
    private static array $lmsHostHasActiveLessonsCache = [];

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

    public static function normalizedCatalogName(string $name): string
    {
        return strtolower(trim($name));
    }

    /**
     * Whether this catalogue name shares the MS Office / MS Office Advanced LMS bundle.
     */
    public function isMsOfficeCatalogPairMember(): bool
    {
        $n = static::normalizedCatalogName((string) $this->name);

        return $n === 'ms office' || $n === 'ms office advanced';
    }

    public function isTallyCatalogPairMember(): bool
    {
        $n = static::normalizedCatalogName((string) $this->name);

        return $n === 'tally' || $n === 'tally erp 9 advanced';
    }

    public function catalogHasActiveLmsLessons(): bool
    {
        return $this->learningModules()
            ->where('is_active', true)
            ->whereHas('lessons', fn ($q) => $q->where('is_active', true))
            ->exists();
    }

    /**
     * Course row whose modules/lessons students should use for this catalogue entry.
     * MS Office and MS Office Advanced share one LMS host (whichever has active lessons; prefers Advanced).
     */
    public function lmsHostCourse(): self
    {
        $id = (int) $this->id;
        if (! array_key_exists($id, static::$lmsHostCourseCache)) {
            static::$lmsHostCourseCache[$id] = $this->resolveLmsHostCourseUncached();
        }

        return static::$lmsHostCourseCache[$id];
    }

    public function lmsHostHasActiveLessons(): bool
    {
        $id = (int) $this->id;
        if (! array_key_exists($id, static::$lmsHostHasActiveLessonsCache)) {
            static::$lmsHostHasActiveLessonsCache[$id] = $this->lmsHostCourse()->catalogHasActiveLmsLessons();
        }

        return static::$lmsHostHasActiveLessonsCache[$id];
    }

    /**
     * Course row whose assessment/question bank students should use for this catalogue entry.
     * Falls back to a shared sibling course when the current course has no usable active exam setup.
     */
    public function assessmentHostCourse(): self
    {
        $id = (int) $this->id;
        if (! array_key_exists($id, static::$assessmentHostCourseCache)) {
            static::$assessmentHostCourseCache[$id] = $this->resolveAssessmentHostCourseUncached();
        }

        return static::$assessmentHostCourseCache[$id];
    }

    public static function forgetLmsHostCourseCache(): void
    {
        static::$lmsHostCourseCache = [];
        static::$lmsHostHasActiveLessonsCache = [];
        static::$assessmentHostCourseCache = [];
    }

    private function resolveLmsHostCourseUncached(): self
    {
        if ($this->catalogHasActiveLmsLessons()) {
            return $this;
        }

        if (! $this->isMsOfficeCatalogPairMember()) {
            return $this;
        }

        $host = static::query()
            ->whereRaw('LOWER(TRIM(name)) IN (?, ?)', ['ms office', 'ms office advanced'])
            ->when(
                $this->training_partner_id === null,
                fn ($q) => $q->whereNull('training_partner_id'),
                fn ($q) => $q->where('training_partner_id', $this->training_partner_id)
            )
            ->whereHas('learningModules', function ($q) {
                $q->where('is_active', true)
                    ->whereHas('lessons', fn ($lq) => $lq->where('is_active', true));
            })
            ->orderByRaw("CASE WHEN LOWER(TRIM(name)) = ? THEN 0 ELSE 1 END", ['ms office advanced'])
            ->orderBy('id')
            ->first();

        return $host ?? $this;
    }

    private function resolveAssessmentHostCourseUncached(): self
    {
        if ($this->hasUsableAssessmentSetup()) {
            return $this;
        }

        $names = $this->sharedAssessmentCatalogNames();
        if ($names === []) {
            return $this;
        }

        $host = static::query()
            ->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
                }
            })
            ->when(
                $this->training_partner_id === null,
                fn ($q) => $q->whereNull('training_partner_id'),
                fn ($q) => $q->where('training_partner_id', $this->training_partner_id)
            )
            ->whereHas('assessments', fn ($q) => $q->where('is_active', true))
            ->whereHas('questionBanks', fn ($q) => $q->where('is_active', true), '>=', 25)
            ->orderBy('id')
            ->first();

        return $host ?? $this;
    }

    private function hasUsableAssessmentSetup(): bool
    {
        return $this->assessments()->where('is_active', true)->exists()
            && $this->questionBanks()->where('is_active', true)->count() >= 25;
    }

    /**
     * @return array<int, string>
     */
    private function sharedAssessmentCatalogNames(): array
    {
        if ($this->isMsOfficeCatalogPairMember()) {
            return ['ms office', 'ms office advanced'];
        }

        if ($this->isTallyCatalogPairMember()) {
            return ['tally', 'tally erp 9 advanced'];
        }

        return [];
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
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
