@extends('layouts.student')

@section('title', 'Set your password')

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-amber-200 overflow-hidden">
        <div class="px-6 py-4 bg-amber-50 border-b border-amber-100">
            <h2 class="text-lg font-semibold text-gray-900">Set a new password</h2>
            <p class="text-sm text-gray-600 mt-1">For security, choose a new password before continuing. You can still use your mobile number to log in until you change it here.</p>
        </div>
        <form method="POST" action="{{ route('student.password.force.update') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <button type="submit" class="w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700">
                Save password and continue
            </button>
        </form>
    </div>
</div>
@endsection
