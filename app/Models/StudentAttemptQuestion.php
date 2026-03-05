<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttemptQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'student_answer',
        'is_correct',
        'marks_obtained'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(StudentAssessmentAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    public function scopeAnswered($query)
    {
        return $query->whereNotNull('student_answer');
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('student_answer');
    }

    // Helper methods
    public function getCorrectAnswerAttribute()
    {
        return $this->question->correct_answer;
    }

    public function getCorrectOptionTextAttribute()
    {
        return $this->question->correct_option_text;
    }

    public function getStudentOptionTextAttribute()
    {
        if (!$this->student_answer) {
            return null;
        }
        return $this->question->{"option_{strtolower($this->student_answer)}"};
    }

    public function isAnswered()
    {
        return !is_null($this->student_answer);
    }

    public function isCorrect()
    {
        return $this->is_correct;
    }

    public function isIncorrect()
    {
        return !$this->is_correct;
    }
}
