<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrainingPartnerActivityLog extends Model
{
    protected $fillable = [
        'training_partner_id',
        'user_id',
        'actor_user_id',
        'type',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public static function recordForUser(
        User $user,
        string $type,
        string $description,
        ?Request $request = null,
        array $metadata = [],
        ?User $actor = null
    ): void {
        if (! Schema::hasTable('training_partner_activity_logs') || ! $user->training_partner_id) {
            return;
        }

        self::create([
            'training_partner_id' => $user->training_partner_id,
            'user_id' => $user->id,
            'actor_user_id' => $actor?->id ?? $user->id,
            'type' => $type,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => $metadata ?: null,
            'occurred_at' => now(),
        ]);
    }
}
