<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMemberAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_member_id',
        'training_partner_id',
        'kiosk_user_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'check_in_status',
        'check_out_status',
        'check_in_image_path',
        'check_out_image_path',
        'check_in_match_distance',
        'check_out_match_distance',
        'check_in_verification_method',
        'check_out_verification_method',
        'check_in_verification_status',
        'check_out_verification_status',
        'check_in_fallback_reason',
        'check_out_fallback_reason',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_distance_meters',
        'check_out_distance_meters',
        'check_in_accuracy_meters',
        'check_out_accuracy_meters',
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_match_distance' => 'decimal:5',
            'check_out_match_distance' => 'decimal:5',
            'check_in_latitude' => 'decimal:7',
            'check_in_longitude' => 'decimal:7',
            'check_out_latitude' => 'decimal:7',
            'check_out_longitude' => 'decimal:7',
        ];
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    public function kioskUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kiosk_user_id');
    }
}
