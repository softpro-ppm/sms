@extends('layouts.admin')

@section('title', $trainingPartner->name)
@section('page-title', $trainingPartner->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $trainingPartner->name }}</h2>
            <p class="text-gray-600 mt-1">{{ $trainingPartner->code }} • {{ $trainingPartner->type }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <a href="{{ route('admin.super.training-partners.edit', $trainingPartner) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.super.training-partners.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Wallet Balance</p>
            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($trainingPartner->wallet_balance, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <p class="text-sm font-medium text-gray-600">Status</p>
            <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full {{ $trainingPartner->status === 'active' ? 'bg-green-100 text-green-800' : ($trainingPartner->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
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
                <li class="px-6 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                    <span class="text-xs text-gray-500">{{ $user->email }} • {{ ucfirst($user->role) }}</span>
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
</div>
@endsection
