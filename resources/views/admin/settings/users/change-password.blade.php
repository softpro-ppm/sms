@extends('layouts.admin')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
<div class="max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('admin.settings.users.index') }}" class="text-primary-600 hover:text-primary-900">
            <i class="fas fa-arrow-left mr-1"></i> Back to Staff Users
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-600 mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.change-password.post', $user) }}" class="p-6 space-y-6">
            @csrf

            @if($user->id === auth()->id())
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror">
                <p class="mt-0.5 text-xs text-gray-500">Minimum 8 characters</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 shadow-sm">
                    Change Password
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
