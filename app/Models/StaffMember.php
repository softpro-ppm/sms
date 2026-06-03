<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_partner_id',
        'created_by',
        'approved_by',
        'staff_code',
        'name',
        'phone',
        'email',
        'designation',
        'department',
        'joining_date',
        'status',
        'face_descriptors',
        'face_image_paths',
        'face_enrolled_at',
        'approved_at',
        'approval_notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'face_descriptors' => 'array',
            'face_image_paths' => 'array',
            'face_enrolled_at' => 'datetime',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function trainingPartner(): BelongsTo
    {
        return $this->belongsTo(TrainingPartner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffMemberAttendance::class);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved' && $this->is_active;
    }
}
