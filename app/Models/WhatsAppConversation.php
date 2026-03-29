<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WhatsAppConversation extends Model
{
    /** Laravel would guess `whats_app_conversations`; migration uses `whatsapp_conversations`. */
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone',
        'student_id',
        'training_partner_id',
        'last_message_at',
        'unread_count',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(WhatsAppMessage::class, 'conversation_id')->latestOfMany('created_at');
    }

    public function displayName(): string
    {
        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->full_name;
        }

        return $this->student?->full_name ?? $this->phone;
    }

    public function scopeForTrainingPartner($query, ?int $trainingPartnerId)
    {
        if ($trainingPartnerId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($trainingPartnerId) {
            $q->where('training_partner_id', $trainingPartnerId)
                ->orWhereHas('student', function ($s) use ($trainingPartnerId) {
                    $s->where('training_partner_id', $trainingPartnerId);
                });
        });
    }
}
