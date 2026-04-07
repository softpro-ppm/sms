@extends('layouts.admin')

@section('title', $trainingPartner->name)
@section('page-title', $trainingPartner->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $trainingPartner->name }}</h2>
            <p class="text-gray-600 mt-1">{{ $trainingPartner->code ?: '— (pending)' }} • {{ $trainingPartner->type }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            @if($trainingPartner->status === 'pending')
            <button type="button" onclick="document.getElementById('approveModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-check mr-2"></i>Approve
            </button>
            <form method="POST" action="{{ route('admin.super.training-partners.reject', $trainingPartner) }}" class="inline" onsubmit="return confirm('Reject this partner?')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
            </form>
            @else
            <button type="button" onclick="document.getElementById('rechargeModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus-circle mr-2"></i>Recharge
            </button>
            @endif
            @if(in_array($trainingPartner->status, ['active', 'suspended']) && $trainingPartner->users->where('role', 'admin')->where('is_active', true)->isNotEmpty())
            <form method="POST" action="{{ route('admin.super.training-partners.impersonate', $trainingPartner) }}" class="inline"
                  onsubmit="return confirm('Open this centre as its admin? You will see their dashboard and catalogue.');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 text-amber-950 rounded-lg hover:bg-amber-400 font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Open as centre admin
                </button>
            </form>
            @endif
            @if(in_array($trainingPartner->status, ['active', 'suspended']))
            <a href="{{ route('admin.super.training-partners.staff.create', $trainingPartner) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-user-plus mr-2"></i>Add Staff
            </a>
            @endif
            <a href="{{ route('admin.super.training-partners.edit', $trainingPartner) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.super.training-partners.activity', $trainingPartner) }}"
               class="inline-flex items-center px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800">
                <i class="fas fa-chart-line mr-2"></i>Full activity
            </a>
            <a href="{{ route('admin.super.training-partners.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Wallet Balance</p>
            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($trainingPartner->wallet_balance, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Student Approval Deduction</p>
            <p class="text-2xl font-bold {{ $trainingPartner->student_approval_deduction > 0 ? 'text-amber-600' : 'text-gray-500' }}">₹{{ number_format($trainingPartner->student_approval_deduction ?? 0, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $trainingPartner->is_hq ? 'HQ: No deduction' : 'Per approved student' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Status</p>
            <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full {{ $trainingPartner->status === 'active' ? 'bg-green-100 text-green-800' : ($trainingPartner->status === 'suspended' ? 'bg-red-100 text-red-800' : ($trainingPartner->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                {{ ucfirst($trainingPartner->status) }}
            </span>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Staff</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $trainingPartner->users_count }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Students</p>
            <p class="text-2xl font-bold text-purple-600">{{ $trainingPartner->students_count }}</p>
        </div>
    </div>

    @if($trainingPartner->logo_path)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Logo</h3>
        <img src="{{ asset('storage/' . $trainingPartner->logo_path) }}" alt="{{ $trainingPartner->name }}" class="h-24 object-contain">
    </div>
    @endif

    @if($trainingPartner->address || $trainingPartner->district || $trainingPartner->contact_name)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($trainingPartner->address)
            <div>
                <dt class="text-sm text-gray-500">Address</dt>
                <dd class="mt-1 text-gray-900">{{ $trainingPartner->address }}</dd>
            </div>
            @endif
            @if($trainingPartner->district)
            <div>
                <dt class="text-sm text-gray-500">District / Mandal</dt>
                <dd class="mt-1 text-gray-900">{{ $trainingPartner->district }}{{ $trainingPartner->mandal ? ' / ' . $trainingPartner->mandal : '' }}</dd>
            </div>
            @endif
            @if($trainingPartner->contact_name)
            <div>
                <dt class="text-sm text-gray-500">Contact</dt>
                <dd class="mt-1 text-gray-900">{{ $trainingPartner->contact_name }}{{ $trainingPartner->contact_phone ? ' • ' . $trainingPartner->contact_phone : '' }}{{ $trainingPartner->contact_email ? ' • ' . $trainingPartner->contact_email : '' }}</dd>
            </div>
            @endif
        </dl>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Staff ({{ $trainingPartner->users->count() }})</h3>
            </div>
            @if($trainingPartner->users->isNotEmpty())
            <ul class="divide-y divide-gray-200">
                @foreach($trainingPartner->users as $user)
                <li class="px-6 py-3 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">{{ $user->email }} • {{ ucfirst($user->role) }}</span>
                        @if($user->role === 'admin' && $user->is_active)
                        <form method="POST" action="{{ route('admin.super.training-partners.impersonate', $trainingPartner) }}" class="inline"
                              onsubmit="return confirm('Sign in as {{ $user->name }}?');">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="text-xs font-semibold text-amber-700 hover:text-amber-900 underline">Enter as</button>
                        </form>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No staff assigned.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Recent Students ({{ $trainingPartner->students_count }} total)</h3>
            </div>
            @if($trainingPartner->students->isNotEmpty())
            <ul class="divide-y divide-gray-200">
                @foreach($trainingPartner->students as $student)
                <li class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900">{{ $student->full_name }}</span>
                    <span class="text-xs text-gray-500">{{ $student->email ?? '—' }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="px-6 py-4 text-sm text-gray-500">No students yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-gray-900">Wallet Transactions</h3>
            <a href="{{ route('admin.super.training-partners.wallet-export', $trainingPartner) }}"
               class="inline-flex items-center text-sm font-medium text-primary-700 hover:text-primary-900">
                <i class="fas fa-download mr-2"></i>Export full history (CSV)
            </a>
        </div>
        @if($trainingPartner->walletTransactions && $trainingPartner->walletTransactions->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance After</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($trainingPartner->walletTransactions as $tx)
                    <tr>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm">{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</td>
                        <td class="px-6 py-3 text-sm font-medium {{ $tx->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $tx->amount >= 0 ? '+' : '' }}₹{{ number_format($tx->amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm">{{ $tx->balance_after !== null ? '₹' . number_format($tx->balance_after, 2) : '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $tx->description ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-6 py-4 text-sm text-gray-500">No transactions yet.</p>
        @endif
    </div>
</div>

@if($trainingPartner->status === 'pending')
<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900/60" onclick="document.getElementById('approveModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Partner</h3>
            <p class="text-sm text-gray-600 mb-4">Assign a unique code and set the student approval deduction amount (₹) to deduct from this partner's wallet when each student is approved.</p>
            <form id="approvePartnerForm" method="POST" action="{{ route('admin.super.training-partners.approve', $trainingPartner) }}"
                  data-is-standard="{{ $trainingPartner->is_standard ? '1' : '0' }}"
                  onsubmit="return confirmStandardPartnerApprove(this);">
                @csrf
                <div class="mb-4">
                    <label for="approve_code" class="block text-sm font-medium text-gray-700 mb-2">Partner Code <span class="text-red-500">*</span></label>
                    <input type="text" id="approve_code" name="code" required maxlength="20"
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="e.g. ABC001" value="{{ old('code') }}">
                    <p class="mt-1 text-xs text-gray-500">Letters, numbers, hyphens. Must be unique.</p>
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label for="approve_deduction" class="block text-sm font-medium text-gray-700 mb-2">Student Approval Deduction (₹) <span class="text-red-500">*</span></label>
                    <input type="number" id="approve_deduction" name="student_approval_deduction" required min="0" step="0.01"
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="0.00">
                    @error('student_approval_deduction')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Recharge Modal -->
<div id="rechargeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900/60" onclick="document.getElementById('rechargeModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recharge Wallet</h3>
            <form method="POST" action="{{ route('admin.super.training-partners.recharge', $trainingPartner) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" required min="0.01" step="0.01"
                               class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="0.00">
                        @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                        <input type="text" id="description" name="description"
                               class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g. Initial funding">
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('rechargeModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus-circle mr-2"></i>Recharge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function confirmStandardPartnerApprove(form) {
    var isStandard = form.getAttribute('data-is-standard') === '1';
    var input = document.getElementById('approve_deduction');
    if (!input) return true;
    var val = parseFloat(input.value);
    if (isStandard && (isNaN(val) || val <= 0)) {
        return confirm('This is a standard training partner with ₹0 per-student approval charge. Usually you set a positive amount so the wallet model works. Approve anyway?');
    }
    return true;
}
</script>
@endsection
