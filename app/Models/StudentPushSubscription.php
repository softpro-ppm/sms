<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPushSubscription extends Model
{
    protected $fillable = [
        'student_id',
        'endpoint',
        'endpoint_hash',
        'public_key',
        'auth_token',
        'content_encoding',
        'user_agent',
        'last_seen_at',
        'enabled',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'enabled' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
