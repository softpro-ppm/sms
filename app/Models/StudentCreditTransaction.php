<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCreditTransaction extends Model
{
    protected $fillable = [
        'student_id',
        'amount',
        'type',
        'notes',
        'reference_enrollment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creditAllocations(): HasMany
    {
        return $this->hasMany(CreditAllocation::class);
    }

    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'enrollment_drop' => 'Dropped from batch',
            'enrollment_remove' => 'Removed from batch',
            'enrollment_transfer' => 'Applied to enrollment',
            'manual_adjustment' => 'Manual adjustment',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
