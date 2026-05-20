@extends('layouts.student')

@section('title', 'Set your password')

@section('content')
<div class="mx-auto max-w-lg px-4 py-8">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-6">
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-100 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">
                <i class="fas fa-shield-halved text-[11px]"></i>
                Security update
            </span>
            <h2 class="mt-4 text-2xl font-semibold text-gray-950">Set a new password</h2>
            <p class="mt-2 text-sm leading-6 text-gray-600">For security, choose a new password before continuing. You can still use your mobile number to sign in until you save the new password here.</p>
        </div>
        <form method="POST" action="{{ route('student.password.force.update') }}" class="space-y-5 p-6">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-primary-700">
                Save password and continue
            </button>
        </form>
    </div>
</div>
@endsection
