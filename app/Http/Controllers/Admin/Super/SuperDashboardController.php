<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $pendingPartners = TrainingPartner::where('status', 'pending')
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        $lowWalletThreshold = (float) env('SMS_LOW_WALLET_THRESHOLD', 500);
        $lowWalletPartners = TrainingPartner::query()
            ->where('type', 'STANDARD')
            ->where('status', 'active')
            ->where('wallet_balance', '<', $lowWalletThreshold)
            ->orderBy('wallet_balance')
            ->limit(20)
            ->get();

        $recentFailedWhatsApp = collect();
        if (Schema::hasTable('whatsapp_logs')) {
            $recentFailedWhatsApp = WhatsAppLog::query()
                ->where('status', 'failed')
                ->latest()
                ->limit(15)
                ->with(['student' => fn ($q) => $q->select('id', 'full_name')])
                ->get();
        }

        $failedJobsCount = 0;
        $recentFailedJobs = collect();
        if (Schema::hasTable('failed_jobs')) {
            $failedJobsCount = (int) DB::table('failed_jobs')->count();
            $recentFailedJobs = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(8)
                ->get();
        }

        return view('admin.super.dashboard', compact(
            'stats',
            'recentPartners',
            'pendingPartners',
            'lowWalletPartners',
            'lowWalletThreshold',
            'recentFailedWhatsApp',
            'failedJobsCount',
            'recentFailedJobs'
        ));
    }
}
