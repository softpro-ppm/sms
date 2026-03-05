<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'difficulty_level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function studentAttemptQuestions()
    {
        return $this->hasMany(StudentAttemptQuestion::class, 'question_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySubject($query, $subject)
    {
        return $query->where('subject', $subject);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    // Helper methods
    public function getCorrectOptionTextAttribute()
    {
        return $this->{"option_{strtolower($this->correct_answer)}"};
    }

    public function isCorrectAnswer($answer)
    {
        return strtoupper($answer) === strtoupper($this->correct_answer);
    }
}
