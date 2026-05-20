<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPushNotificationLog extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'title',
        'message',
        'status',
        'dedupe_key',
        'data',
        'sent_at',
        'sent_on',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'sent_on' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
