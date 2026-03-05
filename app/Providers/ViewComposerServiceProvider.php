<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Student;
use App\Models\Payment;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share common data with admin layout
        View::composer('layouts.admin', function ($view) {
            $pendingStudents = Student::where('status', 'pending')->count();
            $pendingPayments = Payment::where('status', 'pending')->count();
            
            $view->with([
                'pendingStudents' => $pendingStudents,
                'pendingPayments' => $pendingPayments,
            ]);
        });
    }
}