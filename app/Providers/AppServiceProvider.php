<?php

namespace App\Providers;

use App\Models\Enrollment;
use App\Support\AdminLayoutScopes;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('verify-search', function (Request $request) {
            $perMinute = (int) env('VERIFY_SEARCH_RATE_LIMIT_PER_MINUTE', 20);

            return Limit::perMinute(max(1, $perMinute))->by($request->ip());
        });

        if (! app()->runningInConsole() && request()->is('admin/*')) {
            Paginator::defaultView('vendor.pagination.admin-compact');
            Paginator::defaultSimpleView('vendor.pagination.admin-compact');
        }

        View::composer('layouts.admin', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();
            $notifications = collect();
            $notificationCount = 0;

            if ($user->is_admin) {
                $paymentBase = AdminLayoutScopes::pendingPaymentsQuery($user);
                $notifications = (clone $paymentBase)
                    ->with('student:id,full_name')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($payment) {
                        $studentName = $payment->student?->full_name ?? 'Student';

                        return [
                            'title' => 'Payment pending approval',
                            'message' => '₹'.number_format($payment->amount, 2).' from '.$studentName,
                            'time' => $payment->created_at,
                            'type' => 'warning',
                            'url' => route('admin.payments.pending'),
                        ];
                    });

                $notificationCount = AdminLayoutScopes::pendingPaymentsCountCached($user);
            } elseif ($user->is_reception) {
                $studentBase = AdminLayoutScopes::pendingStudentsQuery($user);
                $notifications = (clone $studentBase)
                    ->select('id', 'full_name', 'email', 'created_at')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($student) {
                        $message = $student->full_name;
                        if (! empty($student->email)) {
                            $message .= ' ('.$student->email.')';
                        }

                        return [
                            'title' => 'New student registration',
                            'message' => $message,
                            'time' => $student->created_at,
                            'type' => 'primary',
                            'url' => route('admin.students.index'),
                        ];
                    });

                $notificationCount = AdminLayoutScopes::pendingStudentsCountCached($user);
            }

            $view->with([
                'topbarNotifications' => $notifications,
                'topbarNotificationCount' => $notificationCount,
            ]);
        });

        View::composer('layouts.student', function ($view) {
            $user = auth()->user();
            if (! $user?->is_student || ! $user->student) {
                $view->with(['studentExamsUnlocked' => false]);

                return;
            }

            $unlocked = Enrollment::query()
                ->where('student_id', $user->student->id)
                ->where('status', 'active')
                ->get()
                ->contains(fn (Enrollment $e) => $e->can_take_assessment);

            $view->with(['studentExamsUnlocked' => $unlocked]);
        });
    }
}
