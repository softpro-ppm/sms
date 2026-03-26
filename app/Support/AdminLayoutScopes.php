<?php

namespace App\Support;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Pending student/payment queries for the admin layout (sidebar + bell).
 * Super Admin: platform-wide. TP Admin/Reception: limited to training_partner_id.
 */
final class AdminLayoutScopes
{
    public static function pendingStudentsQuery(?User $user): Builder
    {
        $q = Student::query()->where('status', 'pending');
        if ($user && ! $user->is_super_admin && $user->training_partner_id !== null) {
            $q->where('training_partner_id', $user->training_partner_id);
        }

        return $q;
    }

    public static function pendingPaymentsQuery(?User $user): Builder
    {
        $q = Payment::query()->where('status', 'pending');
        if ($user && ! $user->is_super_admin && $user->training_partner_id !== null) {
            $q->whereHas('student', fn (Builder $s) => $s->where('training_partner_id', $user->training_partner_id));
        }

        return $q;
    }
}
