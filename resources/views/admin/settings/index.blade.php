@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">System Settings</h2>
            <p class="text-gray-600 mt-1">Manage system configuration and preferences</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.settings.users.index') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 shadow-sm">
                <i class="fas fa-user-shield mr-2"></i> Staff Users
            </a>
            <a href="{{ route('admin.settings.email-templates.index') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm">
                <i class="fas fa-envelope-open-text mr-2"></i> Email Templates (Edit All 8)
            </a>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_courses']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-layer-group text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Batches</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_batches']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Exams</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_assessments']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Certificates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_certificates']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-credit-card text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Payments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_payments']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">System Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Application Name</p>
                        <p class="text-sm font-medium">{{ $systemInfo['app_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">PHP Version</p>
                        <p class="text-sm font-medium">{{ $systemInfo['php_version'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Laravel Version</p>
                        <p class="text-sm font-medium">{{ $systemInfo['laravel_version'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Environment</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $systemInfo['environment'] === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($systemInfo['environment']) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Debug Mode</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $systemInfo['debug_mode'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Timezone</p>
                        <p class="text-sm font-medium">{{ $systemInfo['timezone'] }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Server Time</p>
                    <p class="text-sm font-medium">{{ $systemInfo['server_time'] }}</p>
                </div>
            </div>
        </div>

        <!-- Storage Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Storage Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Space</span>
                        <span class="text-sm font-medium">{{ $storageInfo['total_space'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Used Space</span>
                        <span class="text-sm font-medium">{{ $storageInfo['used_space'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Free Space</span>
                        <span class="text-sm font-medium">{{ $storageInfo['free_space'] }}</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php
                        $usedPercentage = (disk_total_space(storage_path()) - disk_free_space(storage_path())) / disk_total_space(storage_path()) * 100;
                    @endphp
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $usedPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 text-center">{{ number_format($usedPercentage, 1) }}% used</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Database Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Database Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Connection</span>
                        <span class="text-sm font-medium">{{ $databaseInfo['connection'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Driver</span>
                        <span class="text-sm font-medium">{{ $databaseInfo['driver'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Cache Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Driver</span>
                        <span class="text-sm font-medium">{{ $cacheInfo['driver'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $cacheInfo['status'] === 'Working' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $cacheInfo['status'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Templates -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Email Templates</h3>
                <p class="text-sm text-gray-500 mt-1">Customize all 8 student notification emails</p>
            </div>
            <a href="{{ route('admin.settings.email-templates.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Manage Templates
            </a>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-3">Registration, Self-Registration, Account Approved, Enrollment, Payment Approved, Fully Paid, Assessment Result, Certificate Issued.</p>
            <a href="{{ route('admin.settings.email-templates.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">→ Open Email Templates →</a>
        </div>
    </div>

    <!-- Mail Configuration -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Mail Configuration</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Driver</p>
                    <p class="text-sm font-medium">{{ $mailInfo['driver'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Host</p>
                    <p class="text-sm font-medium">{{ $mailInfo['host'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Port</p>
                    <p class="text-sm font-medium">{{ $mailInfo['port'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Encryption</p>
                    <p class="text-sm font-medium">{{ $mailInfo['encryption'] ?: 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">From Address</p>
                    <p class="text-sm font-medium">{{ $mailInfo['from_address'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">From Name</p>
                    <p class="text-sm font-medium">{{ $mailInfo['from_name'] ?: 'Not configured' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                            onclick="return confirm('Clear all caches? This may temporarily slow down the application.')">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Cache
                    </button>
                </form>

                <form action="{{ route('admin.settings.optimize') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
                            onclick="return confirm('Optimize application? This will cache configurations for better performance.')">
                        <i class="fas fa-rocket mr-2"></i>
                        Optimize App
                    </button>
                </form>

                <form action="{{ route('admin.settings.backup-database') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors"
                            onclick="return confirm('Create database backup?')">
                        <i class="fas fa-database mr-2"></i>
                        Backup DB
                    </button>
                </form>

                <a href="{{ route('admin.settings.export-data') }}" 
                   class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors"
                   onclick="return confirm('Export all data as JSON?')">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </a>
            </div>
        </div>
    </div>

    <!-- General Settings Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.settings.update-general') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Application Name
                        </label>
                        <input type="text" name="app_name" id="app_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                               value="{{ $systemInfo['app_name'] }}" required>
                    </div>
                    
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone
                        </label>
                        <select name="timezone" id="timezone" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                                required>
                            <option value="UTC" {{ $systemInfo['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="Asia/Kolkata" {{ $systemInfo['timezone'] === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                            <option value="America/New_York" {{ $systemInfo['timezone'] === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            <option value="Europe/London" {{ $systemInfo['timezone'] === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="debug_mode" id="debug_mode" 
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                           {{ $systemInfo['debug_mode'] ? 'checked' : '' }}>
                    <label for="debug_mode" class="ml-2 block text-sm text-gray-900">
                        Enable Debug Mode
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
