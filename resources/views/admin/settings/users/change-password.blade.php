@extends('layouts.admin')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6">
            <a href="{{ route('admin.settings.users.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                <i class="fas fa-arrow-left text-xs"></i> Back to staff users
            </a>
            <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                <i class="fas fa-key text-[10px]"></i>
                Change password
            </div>
            <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Update login credentials for {{ $user->name }}.</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $user->email }}</p>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Password details</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Choose a new password</h3>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.change-password.post', $user) }}" class="space-y-6 p-6">
            @csrf

            @if($user->id === auth()->id())
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700">Current password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                           class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">New password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('password') border-red-500 @enderror">
                <p class="mt-0.5 text-xs text-slate-500">Minimum 8 characters</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm new password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-key text-xs"></i>
                    Change password
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</div>
@endsection
