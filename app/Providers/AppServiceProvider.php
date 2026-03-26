<?php

namespace App\Providers;

use App\Support\AdminLayoutScopes;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.admin', function ($view) {
            if (!auth()->check()) {
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
                            'message' => '₹' . number_format($payment->amount, 2) . ' from ' . $studentName,
                            'time' => $payment->created_at,
                            'type' => 'warning',
                            'url' => route('admin.payments.pending'),
                        ];
                    });

                $notificationCount = (clone $paymentBase)->count();
            } elseif ($user->is_reception) {
                $studentBase = AdminLayoutScopes::pendingStudentsQuery($user);
                $notifications = (clone $studentBase)
                    ->select('id', 'full_name', 'email', 'created_at')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($student) {
                        $message = $student->full_name;
                        if (!empty($student->email)) {
                            $message .= ' (' . $student->email . ')';
                        }

                        return [
                            'title' => 'New student registration',
                            'message' => $message,
                            'time' => $student->created_at,
                            'type' => 'primary',
                            'url' => route('admin.students.index'),
                        ];
                    });

                $notificationCount = (clone $studentBase)->count();
            }

            $view->with([
                'topbarNotifications' => $notifications,
                'topbarNotificationCount' => $notificationCount,
            ]);
        });
    }
}
