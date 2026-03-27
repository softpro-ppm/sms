<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingPartner extends Model
{
    protected $fillable = [
        'type',
        'name',
        'code',
        'logo_path',
        'district',
        'mandal',
        'address',
        'city',
        'state',
        'pincode',
        'contact_name',
        'contact_phone',
        'contact_email',
        'wallet_balance',
        'student_approval_deduction',
        'status',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'student_approval_deduction' => 'decimal:2',
    ];

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(PartnerWalletTransaction::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'training_partner_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'training_partner_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'training_partner_id');
    }

    public function getIsHqAttribute(): bool
    {
        return $this->type === 'HQ';
    }

    public function getIsStandardAttribute(): bool
    {
        return $this->type === 'STANDARD';
    }
}
