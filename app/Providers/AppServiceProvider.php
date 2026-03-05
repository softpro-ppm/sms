<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Student;
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
                $notifications = Payment::with('student:id,full_name')
                    ->where('status', 'pending')
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

                $notificationCount = Payment::where('status', 'pending')->count();
            } elseif ($user->is_reception) {
                $notifications = Student::where('status', 'pending')
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

                $notificationCount = Student::where('status', 'pending')->count();
            }

            $view->with([
                'topbarNotifications' => $notifications,
                'topbarNotificationCount' => $notificationCount,
            ]);
        });
    }
}
