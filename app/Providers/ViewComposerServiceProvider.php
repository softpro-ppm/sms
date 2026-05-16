<?php

namespace App\Providers;

use App\Support\AdminLayoutScopes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share common data with admin layout (TP-scoped for non–Super Admin)
        View::composer('layouts.admin', function ($view) {
            $user = Auth::user();
            $view->with([
                'pendingStudents' => AdminLayoutScopes::pendingStudentsCountCached($user),
                'pendingPayments' => AdminLayoutScopes::pendingPaymentsCountCached($user),
            ]);
        });
    }
}