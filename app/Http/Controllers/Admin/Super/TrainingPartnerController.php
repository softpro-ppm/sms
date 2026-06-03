<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Mail\PartnerApprovedMail;
use App\Models\AssessmentResult;
use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\PartnerWalletTransaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\TrainingPartnerActivityLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class TrainingPartnerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));
        $type = $request->get('type', '');
        $status = $request->get('status', '');

        $query = TrainingPartner::withCount(['users', 'students'])
            ->withExists([
                'users as has_active_admin' => fn ($q) => $q->where('role', 'admin')->where('is_active', true),
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        if ($type !== '' && in_array($type, ['HQ', 'STANDARD'], true)) {
            $query->where('type', $type);
        }

        if ($status !== '' && in_array($status, ['active', 'suspended', 'pending', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $partners = $query->orderBy('type')->orderBy('name')
            ->paginate($perPage)
            ->appends($request->query());

        $stats = [
            'total' => TrainingPartner::count(),
            'active' => TrainingPartner::where('status', 'active')->count(),
            'pending' => TrainingPartner::where('status', 'pending')->count(),
            'hq' => TrainingPartner::where('type', 'HQ')->count(),
            'standard' => TrainingPartner::where('type', 'STANDARD')->count(),
        ];

        return view('admin.super.training-partners.index', compact('partners', 'stats'));
    }

    public function create()
    {
        return view('admin.super.training-partners.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:HQ,STANDARD'],
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:training_partners,code',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'student_approval_deduction' => 'nullable|numeric|min:0',
            'district' => 'nullable|string|max:100',
            'mandal' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'status' => ['required', 'in:active,suspended,pending'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('partner-logos', 'public');
        }
        TrainingPartner::create($data);

        return redirect()->route('admin.super.training-partners.index')
            ->with('success', 'Training partner created successfully.');
    }

    public function show(TrainingPartner $trainingPartner)
    {
        $trainingPartner->loadCount(['users', 'students']);
        $trainingPartner->load(['users', 'students' => fn ($q) => $q->latest()->limit(10)]);

        $trainingPartner->load(['walletTransactions' => fn ($q) => $q->latest()->limit(20)]);

        return view('admin.super.training-partners.show', compact('trainingPartner'));
    }

    public function activity(TrainingPartner $trainingPartner)
    {
        $tpId = $trainingPartner->id;

        $stats = [
            'students_total' => Student::where('training_partner_id', $tpId)->count(),
            'students_approved' => Student::where('training_partner_id', $tpId)->where('status', 'approved')->count(),
            'students_pending' => Student::where('training_partner_id', $tpId)->where('status', 'pending')->count(),
            'batches_with_activity' => Batch::whereHas('enrollments.student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
            'active_enrollments' => Enrollment::where('status', 'active')->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
            'payments_approved_sum' => (float) Payment::where('status', 'approved')->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->sum('amount'),
            'payments_approved_count' => Payment::where('status', 'approved')->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
            'payments_pending_count' => Payment::where('status', 'pending')->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
            'assessment_results' => AssessmentResult::whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
            'certificates_issued' => Certificate::where('is_issued', true)->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))->count(),
        ];

        $recentBatches = Batch::with('course')
            ->whereHas('enrollments.student', fn ($q) => $q->where('training_partner_id', $tpId))
            ->orderByDesc('start_date')
            ->limit(30)
            ->get();

        $recentStudents = Student::where('training_partner_id', $tpId)->latest()->limit(30)->get();

        $recentPayments = Payment::with(['student:id,full_name', 'enrollment.batch.course'])
            ->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            ->latest()
            ->limit(30)
            ->get();

        $recentResults = AssessmentResult::with(['student:id,full_name', 'assessment'])
            ->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            ->latest()
            ->limit(30)
            ->get();

        $recentCertificates = Certificate::with(['student:id,full_name', 'course'])
            ->whereHas('student', fn ($q) => $q->where('training_partner_id', $tpId))
            ->latest()
            ->limit(30)
            ->get();

        $walletBase = PartnerWalletTransaction::query()
            ->where('training_partner_id', $tpId);

        $approvalRevenue = abs((float) (clone $walletBase)
            ->where('type', 'student_approval')
            ->where('amount', '<', 0)
            ->sum('amount'));
        $approvalRevenueMonth = abs((float) (clone $walletBase)
            ->where('type', 'student_approval')
            ->where('amount', '<', 0)
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount'));
        $recharges = (float) (clone $walletBase)
            ->where('type', 'recharge')
            ->where('amount', '>', 0)
            ->sum('amount');
        $approvalDeductionCount = (clone $walletBase)
            ->where('type', 'student_approval')
            ->where('amount', '<', 0)
            ->count();
        $collectedRevenue = abs((float) (clone $walletBase)
            ->where('type', 'student_approval')
            ->where('amount', '<', 0)
            ->where('collection_status', 'collected')
            ->sum('amount'));
        $pendingRevenue = max(0, $approvalRevenue - $collectedRevenue);

        $revenueStats = [
            'approval_revenue' => $approvalRevenue,
            'collected_revenue' => $collectedRevenue,
            'pending_revenue' => $pendingRevenue,
            'approval_revenue_month' => $approvalRevenueMonth,
            'approval_deduction_count' => $approvalDeductionCount,
            'wallet_recharges' => $recharges,
            'wallet_balance' => (float) $trainingPartner->wallet_balance,
            'potential_monthly_revenue' => (float) $stats['students_approved'] * (float) ($trainingPartner->student_approval_deduction ?? 0),
        ];

        $recentRevenueTransactions = (clone $walletBase)
            ->with('collectedBy:id,name')
            ->latest('id')
            ->limit(20)
            ->get();

        $staffUsers = User::query()
            ->where('training_partner_id', $tpId)
            ->whereIn('role', ['admin', 'reception'])
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        $lastSessions = collect();
        if (Schema::hasTable('sessions') && $staffUsers->isNotEmpty()) {
            $lastSessions = DB::table('sessions')
                ->whereIn('user_id', $staffUsers->pluck('id'))
                ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
                ->groupBy('user_id')
                ->pluck('last_activity', 'user_id');
        }

        $staffActivity = $staffUsers->map(function (User $user) use ($lastSessions) {
            $lastActivity = $lastSessions->get($user->id);

            return [
                'user' => $user,
                'last_seen_at' => $lastActivity ? Carbon::createFromTimestamp((int) $lastActivity) : null,
            ];
        });

        $activityTimeline = TrainingPartnerActivityLog::query()
            ->with(['user:id,name,email,role', 'actor:id,name,email,role'])
            ->where('training_partner_id', $tpId)
            ->latest('occurred_at')
            ->limit(30)
            ->get();

        $impersonationLogs = collect();
        if (Schema::hasTable('impersonation_audit_logs')) {
            $impersonationLogs = DB::table('impersonation_audit_logs as l')
                ->join('users as su', 'su.id', '=', 'l.super_admin_user_id')
                ->join('users as tu', 'tu.id', '=', 'l.target_user_id')
                ->select([
                    'l.started_at',
                    'l.ended_at',
                    'su.name as super_admin_name',
                    'tu.name as target_name',
                ])
                ->where('l.training_partner_id', $tpId)
                ->orderByDesc('l.started_at')
                ->limit(10)
                ->get();
        }

        $canImpersonate = in_array($trainingPartner->status, ['active', 'suspended'], true)
            && User::query()
                ->where('training_partner_id', $tpId)
                ->where('role', 'admin')
                ->where('is_active', true)
                ->exists();

        return view('admin.super.training-partners.activity', compact(
            'trainingPartner',
            'stats',
            'recentBatches',
            'recentStudents',
            'recentPayments',
            'recentResults',
            'recentCertificates',
            'revenueStats',
            'recentRevenueTransactions',
            'staffActivity',
            'activityTimeline',
            'impersonationLogs',
            'canImpersonate'
        ));
    }

    public function exportActivityCsv(TrainingPartner $trainingPartner): StreamedResponse
    {
        $filename = 'partner-activity-' . preg_replace('/[^a-zA-Z0-9_-]+/', '', $trainingPartner->code ?: 'partner') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($trainingPartner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Type', 'Description', 'User', 'Actor', 'IP Address', 'User Agent']);

            $trainingPartner->activityLogs()
                ->with(['user:id,name,email', 'actor:id,name,email'])
                ->latest('occurred_at')
                ->chunk(500, function ($logs) use ($out) {
                    foreach ($logs as $log) {
                        fputcsv($out, [
                            $log->occurred_at?->format('Y-m-d H:i:s'),
                            $log->type,
                            $log->description,
                            $log->user?->name . ($log->user?->email ? ' <'.$log->user->email.'>' : ''),
                            $log->actor?->name . ($log->actor?->email ? ' <'.$log->actor->email.'>' : ''),
                            $log->ip_address,
                            $log->user_agent,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportWalletTransactions(TrainingPartner $trainingPartner): StreamedResponse
    {
        $filename = 'wallet-' . preg_replace('/[^a-zA-Z0-9_-]+/', '', $trainingPartner->code ?: 'partner') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($trainingPartner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Type', 'Amount (₹)', 'Balance after (₹)', 'Description', 'Reference']);
            $trainingPartner->walletTransactions()
                ->latest('id')
                ->chunk(500, function ($chunk) use ($out) {
                    foreach ($chunk as $tx) {
                        fputcsv($out, [
                            $tx->created_at?->format('Y-m-d H:i:s'),
                            $tx->type,
                            $tx->amount,
                            $tx->balance_after,
                            $tx->description,
                            ($tx->reference_type ? $tx->reference_type . '#' . $tx->reference_id : ''),
                        ]);
                    }
                });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportRevenueCsv(TrainingPartner $trainingPartner): StreamedResponse
    {
        $filename = 'partner-revenue-' . preg_replace('/[^a-zA-Z0-9_-]+/', '', $trainingPartner->code ?: 'partner') . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($trainingPartner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Partner', $trainingPartner->name]);
            fputcsv($out, ['Code', $trainingPartner->code]);
            fputcsv($out, ['Wallet Balance', $trainingPartner->wallet_balance]);
            fputcsv($out, []);
            fputcsv($out, ['Date', 'Type', 'Amount', 'Revenue Amount', 'Collection Status', 'Collected At', 'Collected By', 'Balance After', 'Description', 'Reference']);

            $trainingPartner->walletTransactions()
                ->with('collectedBy:id,name')
                ->latest('id')
                ->chunk(500, function ($chunk) use ($out) {
                    foreach ($chunk as $tx) {
                        $isRevenue = $tx->type === 'student_approval' && (float) $tx->amount < 0;
                        fputcsv($out, [
                            $tx->created_at?->format('Y-m-d H:i:s'),
                            $tx->type,
                            $tx->amount,
                            $isRevenue ? abs((float) $tx->amount) : 0,
                            $isRevenue ? $tx->collection_status : '',
                            $isRevenue ? $tx->collected_at?->format('Y-m-d H:i:s') : '',
                            $isRevenue ? $tx->collectedBy?->name : '',
                            $tx->balance_after,
                            $tx->description,
                            ($tx->reference_type ? $tx->reference_type . '#' . $tx->reference_id : ''),
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportRevenuePdf(TrainingPartner $trainingPartner)
    {
        $transactions = $trainingPartner->walletTransactions()
            ->latest('id')
            ->limit(500)
            ->get();

        $summary = [
            'approval_revenue' => abs((float) $trainingPartner->walletTransactions()
                ->where('type', 'student_approval')
                ->where('amount', '<', 0)
                ->sum('amount')),
            'collected_revenue' => abs((float) $trainingPartner->walletTransactions()
                ->where('type', 'student_approval')
                ->where('amount', '<', 0)
                ->where('collection_status', 'collected')
                ->sum('amount')),
            'approval_revenue_month' => abs((float) $trainingPartner->walletTransactions()
                ->where('type', 'student_approval')
                ->where('amount', '<', 0)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount')),
            'recharges' => (float) $trainingPartner->walletTransactions()
                ->where('type', 'recharge')
                ->where('amount', '>', 0)
                ->sum('amount'),
            'wallet_balance' => (float) $trainingPartner->wallet_balance,
        ];

        $pdf = Pdf::loadView('admin.super.training-partners.revenue-pdf', compact(
            'trainingPartner',
            'transactions',
            'summary'
        ));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('partner-revenue-' . ($trainingPartner->code ?: $trainingPartner->id) . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function markRevenueCollected(Request $request, TrainingPartner $trainingPartner, PartnerWalletTransaction $walletTransaction)
    {
        if ((int) $walletTransaction->training_partner_id !== (int) $trainingPartner->id) {
            abort(404);
        }

        if (! $walletTransaction->is_revenue) {
            return redirect()->back()->with('error', 'Only student approval revenue deductions can be marked as collected.');
        }

        if ($walletTransaction->collection_status === 'collected') {
            return redirect()->back()->with('info', 'This revenue item is already marked as collected.');
        }

        $validated = $request->validate([
            'collection_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $walletTransaction->update([
            'collection_status' => 'collected',
            'collected_at' => now(),
            'collected_by' => auth()->id(),
            'collection_notes' => $validated['collection_notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Revenue marked as collected.');
    }

    public function recharge(Request $request, TrainingPartner $trainingPartner)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $amount = (float) $request->amount;
        $newBalance = $trainingPartner->wallet_balance + $amount;

        $trainingPartner->increment('wallet_balance', $amount);
        $trainingPartner->walletTransactions()->create([
            'amount' => $amount,
            'type' => 'recharge',
            'description' => $request->description ?: 'Wallet recharge by Super Admin',
            'balance_after' => $newBalance,
        ]);

        return redirect()->back()
            ->with('success', "Recharged ₹" . number_format($amount, 2) . " to {$trainingPartner->name}. New balance: ₹" . number_format($newBalance, 2));
    }

    public function edit(TrainingPartner $trainingPartner)
    {
        return view('admin.super.training-partners.edit', compact('trainingPartner'));
    }

    public function update(Request $request, TrainingPartner $trainingPartner)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:HQ,STANDARD'],
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:20', Rule::unique('training_partners', 'code')->ignore($trainingPartner->id)],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'student_approval_deduction' => 'nullable|numeric|min:0',
            'district' => 'nullable|string|max:100',
            'mandal' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'status' => ['required', 'in:active,suspended,pending'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            if ($trainingPartner->logo_path) {
                Storage::disk('public')->delete($trainingPartner->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('partner-logos', 'public');
        }
        $trainingPartner->update($data);

        return redirect()->route('admin.super.training-partners.index')
            ->with('success', 'Training partner updated successfully.');
    }

    public function approve(Request $request, TrainingPartner $trainingPartner)
    {
        if ($trainingPartner->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending partners can be approved.');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|alpha_dash|unique:training_partners,code,' . $trainingPartner->id,
            'student_approval_deduction' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $amount = (float) $request->student_approval_deduction;
        $code = trim($request->code);
        $trainingPartner->update([
            'code' => $code,
            'status' => 'active',
            'student_approval_deduction' => $amount,
        ]);

        $loginCredentials = null;
        $email = $trainingPartner->contact_email;
        $hasAdmin = $trainingPartner->users()->where('role', 'admin')->exists();

        if ($email && !$hasAdmin && !User::where('email', $email)->exists()) {
            $tempPassword = Str::random(10);
            User::create([
                'name' => $trainingPartner->contact_name ?: $trainingPartner->name,
                'email' => $email,
                'password' => Hash::make($tempPassword),
                'role' => 'admin',
                'training_partner_id' => $trainingPartner->id,
                'is_active' => true,
                'must_change_password' => true,
            ]);
            $loginCredentials = ['email' => $email, 'password' => $tempPassword];
        }

        if ($email) {
            try {
                Mail::to($email)->send(new PartnerApprovedMail($trainingPartner, $loginCredentials));
            } catch (\Throwable $e) {
                Log::warning('Partner approval email failed', ['partner' => $trainingPartner->id, 'email' => $email, 'error' => $e->getMessage()]);
            }
        }

        $msg = "{$trainingPartner->name} approved with code {$code}. Student approval deduction set to ₹" . number_format($amount, 2);
        if ($loginCredentials) {
            $msg .= '. TP Admin created. Credentials sent to ' . $email . '.';
        } elseif ($email) {
            $msg .= '. Approval email sent to ' . $email . '.';
        }
        return redirect()->back()->with('success', $msg);
    }

    public function reject(TrainingPartner $trainingPartner)
    {
        if ($trainingPartner->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending partners can be rejected.');
        }

        $trainingPartner->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', "{$trainingPartner->name} has been rejected.");
    }

    public function createStaff(TrainingPartner $trainingPartner)
    {
        return view('admin.super.training-partners.create-staff', compact('trainingPartner'));
    }

    public function storeStaff(Request $request, TrainingPartner $trainingPartner)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'training_partner_id' => $trainingPartner->id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.super.training-partners.show', $trainingPartner)
            ->with('success', "{$data['role']} user {$data['name']} created. Credentials: {$data['email']} / (as set).");
    }

    public function destroy(TrainingPartner $trainingPartner)
    {
        $trainingPartner->loadCount(['users', 'students']);

        if ($trainingPartner->is_hq) {
            return redirect()->back()
                ->with('error', 'Cannot delete the HQ training partner.');
        }

        if ($trainingPartner->users_count > 0 || $trainingPartner->students_count > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete: partner has staff or students. Suspend instead.');
        }

        $trainingPartner->delete();

        return redirect()->route('admin.super.training-partners.index')
            ->with('success', 'Training partner deleted.');
    }
}
