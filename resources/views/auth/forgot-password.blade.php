@extends('layouts.login-split')

@section('content')
<div class="min-h-[calc(100vh-140px)] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-14 w-auto bg-white rounded-xl p-2 shadow-lg mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mt-3">Reset Password</h1>
            <p class="text-gray-600 mt-1">Staff & administrator accounts only</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                    <i class="fas fa-key text-amber-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Forgot Password?</h2>
                    <p class="text-sm text-gray-500">Enter your staff email to receive a reset link</p>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                               placeholder="Enter your staff email">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>

            <p class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
