<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\TrainingPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrainingPartnerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));
        $type = $request->get('type', '');
        $status = $request->get('status', '');

        $query = TrainingPartner::withCount(['users', 'students']);

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
            'student_approval_deduction' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $amount = (float) $request->student_approval_deduction;
        $trainingPartner->update([
            'status' => 'active',
            'student_approval_deduction' => $amount,
        ]);

        return redirect()->back()
            ->with('success', "{$trainingPartner->name} approved. Student approval deduction set to ₹" . number_format($amount, 2));
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
