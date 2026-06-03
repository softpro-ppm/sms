<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_partner_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'check_in_image_path',
        'check_out_image_path',
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }
}
