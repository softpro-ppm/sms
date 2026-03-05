<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'time_limit_minutes',
        'total_questions',
        'passing_percentage',
        'is_active'
    ];

    protected $casts = [
        'passing_percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function studentAttempts(): HasMany
    {
        return $this->hasMany(StudentAssessmentAttempt::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'course_id', 'course_id');
    }

    // Accessors
    public function getActiveQuestionsAttribute()
    {
        return $this->questions()->where('is_active', true)->get();
    }

    public function getRandomQuestionsAttribute()
    {
        return $this->active_questions->shuffle()->take($this->total_questions);
    }

    // New methods for enhanced assessment system
    public function getAvailableSubjectsAttribute()
    {
        return $this->questionBanks()
            ->active()
            ->select('subject')
            ->distinct()
            ->pluck('subject')
            ->toArray();
    }

    public function getQuestionsBySubject($subject, $count = 4)
    {
        return $this->questionBanks()
            ->active()
            ->bySubject($subject)
            ->inRandomOrder()
            ->take($count)
            ->get();
    }

    public function canGenerateAssessment()
    {
        $subjects = $this->available_subjects;
        if (count($subjects) < 5) {
            return false;
        }

        foreach ($subjects as $subject) {
            $questionCount = $this->questionBanks()
                ->active()
                ->bySubject($subject)
                ->count();
            if ($questionCount < 2) {
                return false;
            }
        }

        return true;
    }

    public function generateRandomQuestions($count = 25, $setCount = 3)
    {
        $questionPool = $this->questionBanks()->active()->get();
        if ($questionPool->isEmpty()) {
            return [];
        }

        $subjects = $questionPool->groupBy('subject');
        $subjectNames = $subjects->keys();

        $minPerSubject = ($subjectNames->count() * 2 <= $count) ? 2 : 1;
        $sets = [];

        for ($setIndex = 0; $setIndex < $setCount; $setIndex++) {
            $selected = collect();
            $usedIds = [];

            foreach ($subjectNames as $subject) {
                $available = $subjects[$subject]
                    ->whereNotIn('id', $usedIds)
                    ->shuffle();

                $take = min($minPerSubject, $available->count());
                if ($take > 0) {
                    $picked = $available->take($take);
                    $selected = $selected->merge($picked);
                    $usedIds = array_merge($usedIds, $picked->pluck('id')->all());
                }
            }

            $remaining = $count - $selected->count();
            if ($remaining > 0) {
                $remainingPool = $questionPool
                    ->whereNotIn('id', $usedIds)
                    ->shuffle()
                    ->take($remaining);
                $selected = $selected->merge($remainingPool);
            }

            $sets[] = $selected->shuffle()->values();
        }

        return $sets;
    }
}
