<?php

namespace App\Support;

use App\Models\StudentDeletionRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Pending student/payment queries for the admin layout (sidebar + bell).
 * Super Admin: platform-wide. TP Admin/Reception: limited to training_partner_id.
 */
final class AdminLayoutScopes
{
    /** Sidebar badge counts cache TTL ( seconds ); staleness acceptable per Sprint 1. */
    public const PENDING_COUNTS_TTL_SECONDS = 60;

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

    public static function pendingDeletionRequestsQuery(?User $user): Builder
    {
        $q = StudentDeletionRequest::query()
            ->where('status', StudentDeletionRequest::STATUS_PENDING);

        if ($user && ! $user->is_super_admin && $user->training_partner_id !== null) {
            $q->whereHas('student', fn (Builder $s) => $s->where('training_partner_id', $user->training_partner_id));
        }

        return $q;
    }

    public static function pendingStudentsCountCached(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return Cache::remember(
            self::pendingCountsCacheKey($user, 'students'),
            self::PENDING_COUNTS_TTL_SECONDS,
            fn (): int => (int) self::pendingStudentsQuery($user)->count()
        );
    }

    public static function pendingPaymentsCountCached(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return Cache::remember(
            self::pendingCountsCacheKey($user, 'payments'),
            self::PENDING_COUNTS_TTL_SECONDS,
            fn (): int => (int) self::pendingPaymentsQuery($user)->count()
        );
    }

    public static function pendingDeletionRequestsCountCached(?User $user): int
    {
        if (! $user) {
            return 0;
        }

        return Cache::remember(
            self::pendingCountsCacheKey($user, 'deletion_requests'),
            self::PENDING_COUNTS_TTL_SECONDS,
            fn (): int => (int) self::pendingDeletionRequestsQuery($user)->count()
        );
    }

    public static function clearPendingCountsForTrainingPartner(?int $trainingPartnerId): void
    {
        User::query()
            ->where(function (Builder $query) use ($trainingPartnerId) {
                $query->where('role', 'super_admin');

                if ($trainingPartnerId !== null) {
                    $query->orWhere('training_partner_id', $trainingPartnerId);
                }
            })
            ->get(['id', 'role', 'training_partner_id'])
            ->each(function (User $user): void {
                Cache::forget(self::pendingCountsCacheKey($user, 'students'));
                Cache::forget(self::pendingCountsCacheKey($user, 'payments'));
                Cache::forget(self::pendingCountsCacheKey($user, 'deletion_requests'));
            });
    }

    private static function pendingCountsCacheKey(User $user, string $suffix): string
    {
        $scope = $user->is_super_admin ? 'super' : 'tp_'.($user->training_partner_id ?? 'null');

        return 'admin_pending.u'.$user->id.'.'.$scope.'.'.$suffix;
    }
}
