<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Mail\PartnerApprovedMail;
use App\Models\AssessmentResult;
use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
            'canImpersonate'
        ));
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
