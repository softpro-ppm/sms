<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuperDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_partners' => TrainingPartner::count(),
            'active_partners' => TrainingPartner::where('status', 'active')->count(),
            'pending_partners' => TrainingPartner::where('status', 'pending')->count(),
            'hq_partners' => TrainingPartner::where('type', 'HQ')->count(),
            'standard_partners' => TrainingPartner::where('type', 'STANDARD')->count(),
            'total_students' => Student::count(),
            'students_pending' => Student::where('status', 'pending')->count(),
            'total_staff' => User::whereIn('role', ['admin', 'reception'])->count(),
            'pending_payments_all' => Payment::where('status', 'pending')->count(),
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

        $superWorkspace = $this->buildSuperWorkspace(
            $lowWalletThreshold,
            $pendingPartners,
            $lowWalletPartners,
            $recentFailedWhatsApp,
            $failedJobsCount
        );

        return view('admin.super.dashboard', compact(
            'stats',
            'recentPartners',
            'pendingPartners',
            'lowWalletPartners',
            'lowWalletThreshold',
            'recentFailedWhatsApp',
            'failedJobsCount',
            'recentFailedJobs',
            'superWorkspace'
        ));
    }

    private function buildSuperWorkspace(
        float $lowWalletThreshold,
        Collection $pendingPartners,
        Collection $lowWalletPartners,
        Collection $recentFailedWhatsApp,
        int $failedJobsCount
    ): array {
        $inactivePartnersCount = TrainingPartner::where('status', 'suspended')->count();

        $partnerBacklog = TrainingPartner::query()
            ->withCount([
                'students',
                'students as pending_students_count' => fn ($query) => $query->where('status', 'pending'),
            ])
            ->withCount([
                'users as staff_count' => fn ($query) => $query->whereIn('role', ['admin', 'reception']),
            ])
            ->get()
            ->map(function (TrainingPartner $partner) {
                $pendingPayments = Payment::where('status', 'pending')
                    ->whereHas('student', fn ($query) => $query->where('training_partner_id', $partner->id))
                    ->count();

                $score = ((int) $partner->pending_students_count * 2) + ($pendingPayments * 2) + ($partner->status !== 'active' ? 3 : 0);
                if ($partner->is_standard && (float) $partner->wallet_balance < (float) $partner->student_approval_deduction * 5) {
                    $score += 2;
                }

                return [
                    'partner' => $partner,
                    'pending_payments_count' => $pendingPayments,
                    'attention_score' => $score,
                ];
            })
            ->sortByDesc('attention_score')
            ->values();

        $highBacklogPartners = $partnerBacklog
            ->filter(fn ($item) => $item['attention_score'] > 0)
            ->take(6)
            ->values();

        return [
            'queue_counts' => [
                'pending_partners' => $pendingPartners->count(),
                'low_wallet' => $lowWalletPartners->count(),
                'inactive_partners' => $inactivePartnersCount,
                'high_backlog' => $highBacklogPartners->count(),
                'failed_whatsapp' => $recentFailedWhatsApp->count(),
                'failed_jobs' => $failedJobsCount,
            ],
            'pending_partners' => $pendingPartners->take(5)->values(),
            'low_wallet_partners' => $lowWalletPartners->take(6)->values(),
            'high_backlog_partners' => $highBacklogPartners,
            'partner_health' => $partnerBacklog->take(8)->values(),
            'low_wallet_threshold' => $lowWalletThreshold,
        ];
    }
}
