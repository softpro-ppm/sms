<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'conversation_id',
        'direction',
        'meta_message_id',
        'type',
        'body',
        'media_url',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }
}
