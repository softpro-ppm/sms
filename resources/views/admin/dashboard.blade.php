@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div id="welcome-banner" class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-2xl p-8 text-white transition-all duration-500 ease-in-out">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-primary-100 text-lg">Here's what's happening with your institute today.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <img src="{{ asset('images/logo/Logo.jpg') }}" 
                         alt="SoftPro Logo" 
                         class="w-16 h-16 rounded-full object-cover">
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-sm text-success-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+12% from last month</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Students -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_students']) }}</p>
                    <p class="text-sm text-warning-600 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Awaiting review</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payments</p>
                    <p class="text-3xl font-bold text-gray-900">₹{{ number_format($stats['total_payments']) }}</p>
                    <p class="text-sm text-success-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+8% from last month</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_payments']) }}</p>
                    <p class="text-sm text-warning-600 flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span>Requires approval</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.students.create') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Register Student</p>
                    <p class="text-sm text-gray-600">Add new student</p>
                </div>
            </a>
            
            <a href="{{ route('admin.payments.create') }}" class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus-circle text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Record Payment</p>
                    <p class="text-sm text-gray-600">Add payment</p>
                </div>
            </a>
            
            <a href="{{ route('admin.payments.index') }}" class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all duration-200">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-double text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Approve Payments</p>
                    <p class="text-sm text-gray-600">{{ $stats['pending_payments'] }} pending</p>
                </div>
            </a>
            
            <a href="{{ route('admin.students.index') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-200">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">View Students</p>
                    <p class="text-sm text-gray-600">Manage students</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h3>
            <div class="space-y-4">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-rupee-sign text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</p>
                            <p class="text-sm text-gray-600">₹{{ number_format($payment->amount) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($payment->status === 'approved') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $payment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-money-bill-wave text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent payments</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Students -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Students</h3>
            <div class="space-y-4">
                @forelse($recentStudents as $student)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-semibold">{{ substr($student->full_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                            <p class="text-sm text-gray-600">{{ $student->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($student->status === 'approved') bg-green-100 text-green-800
                            @elseif($student->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($student->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $student->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent students</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Auto-hide welcome banner after 7 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const welcomeBanner = document.getElementById('welcome-banner');
        if (welcomeBanner) {
            setTimeout(() => {
                welcomeBanner.style.opacity = '0';
                welcomeBanner.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    welcomeBanner.style.display = 'none';
                }, 500);
            }, 7000);
        }
    });

</script>
@endsection
