@extends('layouts.login-split')

@section('content')
<div class="min-h-[calc(100vh-140px)] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Global error (from failed login) -->
        @if($errors->any())
            <div class="max-w-2xl mx-auto mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">{{ $errors->first('email') }}</p>
            </div>
        @endif

        <!-- Centered logo + title (above split on all screens) -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-14 w-auto bg-white rounded-xl p-2 shadow-lg mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mt-3">Student Management System</h1>
            <p class="text-gray-600 mt-1">Sign in to your account</p>
        </div>

        <!-- Mobile/Tablet: Tabs (visible only below lg) | Desktop: both forms visible -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12" x-data="{ activeTab: 'student' }">
            <!-- Tabs: Mobile/Tablet only -->
            <div class="lg:col-span-2 lg:hidden flex border-b border-gray-200 -mt-2 mb-2">
                <button type="button"
                        @click="activeTab = 'student'"
                        :class="activeTab === 'student' ? 'border-amber-500 text-amber-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 px-4 text-center border-b-2 transition-colors">
                    <i class="fas fa-user-graduate mr-2"></i>Student Login
                </button>
                <button type="button"
                        @click="activeTab = 'staff'"
                        :class="activeTab === 'staff' ? 'border-amber-500 text-amber-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-3 px-4 text-center border-b-2 transition-colors">
                    <i class="fas fa-user-tie mr-2"></i>Reception / Admin
                </button>
            </div>

            <!-- LEFT: Student Login (on mobile: show when tab=student; on lg: always show) -->
            <div class="lg:order-1 transition-opacity duration-200"
                 :class="activeTab === 'student' ? 'block' : 'hidden lg:block'">
                <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-graduate text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">STUDENT LOGIN</h2>
                            <p class="text-sm text-gray-500">Access your student dashboard</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="student_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input id="student_email" name="email" type="email" autocomplete="email" required
                                       value="{{ old('email') }}"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-red-500 @enderror"
                                       placeholder="Enter your email">
                            </div>
                        </div>
                        <div>
                            <label for="student_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="student_password" name="password" type="password" autocomplete="current-password" required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input name="remember" type="checkbox" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                        </div>
                        <button type="submit" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-black font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Sign in
                        </button>
                        <p class="text-center text-sm text-gray-600">
                            Don't have an account? <a href="{{ route('register') }}" class="font-medium text-gray-900 hover:text-amber-600">Register here</a>
                        </p>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Reception / Admin Login (on mobile: show when tab=staff; on lg: always show) -->
            <div class="lg:order-2 transition-opacity duration-200"
                 :class="activeTab === 'staff' ? 'block' : 'hidden lg:block'">
                <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-tie text-amber-400 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">RECEPTION / ADMIN LOGIN</h2>
                            <p class="text-sm text-gray-500">Staff and administrator access</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="staff_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input id="staff_email" name="email" type="email" autocomplete="email" required
                                       value="{{ old('email') }}"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Enter your email">
                            </div>
                        </div>
                        <div>
                            <label for="staff_password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="staff_password" name="password" type="password" autocomplete="current-password" required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Enter your password">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input name="remember" type="checkbox" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                            <a href="#" class="text-sm font-medium text-gray-900 hover:text-amber-600">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i> Sign in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
