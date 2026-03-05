<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'status'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function attemptQuestions()
    {
        return $this->hasMany(StudentAttemptQuestion::class, 'attempt_id');
    }

    public function assessmentResult()
    {
        return $this->hasOne(AssessmentResult::class, 'attempt_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Helper methods
    public function getDurationAttribute()
    {
        if ($this->completed_at && $this->started_at) {
            return $this->completed_at->diffInMinutes($this->started_at);
        }
        return null;
    }

    public function getTotalQuestionsAttribute()
    {
        return $this->attemptQuestions()->count();
    }

    public function getCorrectAnswersAttribute()
    {
        return $this->attemptQuestions()->where('is_correct', true)->count();
    }

    public function getTotalMarksAttribute()
    {
        return $this->attemptQuestions()->sum('marks_obtained');
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isAbandoned()
    {
        return $this->status === 'abandoned';
    }
}
