<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentResult extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_id',
        'enrollment_id',
        'attempt_id',
        'attempt_number',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'total_marks',
        'percentage',
        'grade',
        'is_passed',
        'subject_wise_marks',
        'started_at',
        'completed_at',
        'time_taken_minutes',
        'answers'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'subject_wise_marks' => 'array'
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(StudentAssessmentAttempt::class, 'attempt_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // Accessors
    public function getGradeAttribute(): string
    {
        if (isset($this->attributes['grade']) && $this->attributes['grade']) {
            return $this->attributes['grade'];
        }
        
        // Fallback: A+ (80%+), A (60-80%), B (35-60%), C (below 35%)
        $percentage = $this->percentage ?? 0;
        
        if ($percentage >= 80) return 'A+';
        if ($percentage >= 60) return 'A';
        if ($percentage >= 35) return 'B';
        return 'C';
    }

    public function getTimeTakenFormattedAttribute(): string
    {
        $timeTaken = $this->time_taken_minutes ?? 0;
        $hours = floor($timeTaken / 60);
        $minutes = $timeTaken % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    // New methods for enhanced assessment system
    public function getSubjectWiseMarksAttribute()
    {
        if (isset($this->attributes['subject_wise_marks'])) {
            return json_decode($this->attributes['subject_wise_marks'], true);
        }
        
        // Calculate subject-wise marks from attempt questions
        if ($this->attempt) {
            $subjectMarks = [];
            $questions = $this->attempt->attemptQuestions()->with('question')->get();
            
            foreach ($questions as $attemptQuestion) {
                $subject = $attemptQuestion->question->subject;
                if (!isset($subjectMarks[$subject])) {
                    $subjectMarks[$subject] = ['total' => 0, 'correct' => 0, 'marks' => 0];
                }
                $subjectMarks[$subject]['total']++;
                if ($attemptQuestion->is_correct) {
                    $subjectMarks[$subject]['correct']++;
                    $subjectMarks[$subject]['marks'] += 4;
                }
            }
            
            return $subjectMarks;
        }
        
        return [];
    }

    public function isPassed()
    {
        return $this->is_passed || $this->percentage >= 35;
    }

    public function getPassingStatusAttribute()
    {
        if ($this->isPassed()) {
            return 'Passed';
        }
        return 'Failed';
    }

    public function canRetake()
    {
        return !$this->isPassed();
    }
}
