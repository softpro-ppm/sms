<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerWalletTransaction extends Model
{
    protected $fillable = [
        'training_partner_id',
        'amount',
        'type',
        'reference_type',
        'reference_id',
        'description',
        'balance_after',
        'collection_status',
        'collected_at',
        'collected_by',
        'collection_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'collected_at' => 'datetime',
    ];

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function getIsRevenueAttribute(): bool
    {
        return $this->type === 'student_approval' && (float) $this->amount < 0;
    }
}
