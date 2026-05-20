@extends('layouts.admin')

@section('title', 'Add Staff User')
@section('page-title', 'Add Staff User')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6">
            <a href="{{ route('admin.settings.users.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                <i class="fas fa-arrow-left text-xs"></i> Back to staff users
            </a>
            <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                <i class="fas fa-user-plus text-[10px]"></i>
                Add staff user
            </div>
            <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Create a new admin or reception account.</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Set the staff member’s access level and create a secure login for operational use.</p>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">User details</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Account information</h3>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.store') }}" class="space-y-6 p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('password') border-red-500 @enderror">
                <p class="mt-0.5 text-xs text-slate-500">Minimum 8 characters</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-slate-700">Role <span class="text-red-500">*</span></label>
                <select name="role" id="role" required
                        class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100 @error('role') border-red-500 @enderror">
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="reception" {{ old('role') === 'reception' ? 'selected' : '' }}>Reception</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-user-plus text-xs"></i>
                    Create user
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</div>
@endsection
