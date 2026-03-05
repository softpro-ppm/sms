<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'assessment_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'options',
        'correct_answer',
        'explanation',
        'difficulty_level',
        'difficulty',
        'marks',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'options' => 'array'
    ];

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    // Accessors
    public function getOptionsAttribute(): array
    {
        // If we have individual option fields, use them
        if ($this->option_a || $this->option_b || $this->option_c || $this->option_d) {
            return [
                $this->option_a,
                $this->option_b,
                $this->option_c,
                $this->option_d
            ];
        }
        
        // Otherwise, use the JSON options field
        $jsonOptions = $this->attributes['options'] ?? null;
        if ($jsonOptions) {
            return json_decode($jsonOptions, true) ?: [];
        }
        
        return [];
    }

    public function getCorrectOptionAttribute(): string
    {
        return $this->options[$this->correct_answer];
    }
}
