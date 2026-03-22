<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\TrainingPartner;
use App\Models\Student;
use App\Models\User;

class SuperDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_partners' => TrainingPartner::count(),
            'active_partners' => TrainingPartner::where('status', 'active')->count(),
            'hq_partners' => TrainingPartner::where('type', 'HQ')->count(),
            'standard_partners' => TrainingPartner::where('type', 'STANDARD')->count(),
            'total_students' => Student::count(),
            'total_staff' => User::whereIn('role', ['admin', 'reception'])->count(),
        ];

        $recentPartners = TrainingPartner::latest('created_at')->limit(5)->get();

        return view('admin.super.dashboard', compact('stats', 'recentPartners'));
    }
}
