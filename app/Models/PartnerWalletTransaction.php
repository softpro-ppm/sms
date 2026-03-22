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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }
}
