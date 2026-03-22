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

        // Pass guarantee: 60-80% of questions should have correct_answer = 'A'
        $questionsWithA = $questionPool->filter(fn ($q) => strtoupper($q->correct_answer ?? '') === 'A');
        $questionsNotA = $questionPool->filter(fn ($q) => strtoupper($q->correct_answer ?? '') !== 'A');

        $minWithA = (int) ceil($count * 0.60); // 15 for count=25
        $maxWithA = (int) floor($count * 0.80); // 20 for count=25
        $targetWithA = min($questionsWithA->count(), rand($minWithA, $maxWithA));
        $targetNotA = $count - $targetWithA;

        $sets = [];

        for ($setIndex = 0; $setIndex < $setCount; $setIndex++) {
            $selected = collect();
            $usedIds = [];

            // Pick questions with correct_answer A (60-80% of paper)
            $availableA = $questionsWithA->whereNotIn('id', $usedIds)->shuffle();
            $pickedA = $availableA->take(min($targetWithA, $availableA->count()));
            $selected = $selected->merge($pickedA);
            $usedIds = array_merge($usedIds, $pickedA->pluck('id')->all());

            // Fill remaining with other questions (B/C/D)
            $remaining = $count - $selected->count();
            if ($remaining > 0) {
                $remainingPool = $questionsNotA
                    ->whereNotIn('id', $usedIds)
                    ->shuffle()
                    ->take($remaining);
                $selected = $selected->merge($remainingPool);
                $usedIds = array_merge($usedIds, $remainingPool->pluck('id')->all());
                // If still short, fill from any unused questions
                $stillRemaining = $count - $selected->count();
                if ($stillRemaining > 0) {
                    $fillPool = $questionPool->whereNotIn('id', $usedIds)->shuffle()->take($stillRemaining);
                    $selected = $selected->merge($fillPool);
                }
            }

            $sets[] = $selected->shuffle()->values();
        }

        return $sets;
    }
}
