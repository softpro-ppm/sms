@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-sliders-h text-[10px]"></i>
                    System settings
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Manage operational settings, system health, and platform tools.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review centre-wide settings, staff access, messaging templates, and system maintenance actions from one control area.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.settings.users.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-medium text-violet-700 transition hover:border-violet-300 hover:bg-violet-100">
                    <i class="fas fa-user-shield text-xs"></i> Staff users
                </a>
                <a href="{{ route('admin.staff-attendance.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                    <i class="fas fa-list-check text-xs"></i> Attendance
                </a>
                <a href="{{ route('admin.staff-members.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">
                    <i class="fas fa-id-badge text-xs"></i> Staff profiles
                </a>
                @if($showFullSystemPanels ?? false)
                <a href="{{ route('admin.settings.email-templates.index') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                    <i class="fas fa-envelope-open-text text-xs"></i> Email templates
                </a>
                @endif
            </div>
        </div>
    </section>

    @if(!($showFullSystemPanels ?? false))
    <div class="rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-900">
        <strong>{{ $trainingPartnerName ?? 'Your centre' }}</strong> — summary counts below include only this training partner’s students, payments, batches, exams, and certificates. Server storage, database, and global configuration are managed by the platform administrator.
    </div>
    @endif

    <!-- System Statistics (TP/HQ admins: scoped to training_partner_id) -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Students</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_students']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Courses</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_courses']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Batches</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_batches']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Exams</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_assessments']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Certificates</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_certificates']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Payments</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_payments']) }}</p>
        </div>
    </div>

    @if($showFullSystemPanels ?? true)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Information -->
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">System</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">System information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Application Name</p>
                        <p class="text-sm font-medium text-slate-900">{{ $systemInfo['app_name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">PHP Version</p>
                        <p class="text-sm font-medium text-slate-900">{{ $systemInfo['php_version'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Laravel Version</p>
                        <p class="text-sm font-medium text-slate-900">{{ $systemInfo['laravel_version'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Environment</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                            {{ $systemInfo['environment'] === 'production' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($systemInfo['environment']) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Debug Mode</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                            {{ $systemInfo['debug_mode'] ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $systemInfo['debug_mode'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Timezone</p>
                        <p class="text-sm font-medium text-slate-900">{{ $systemInfo['timezone'] }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Server Time</p>
                    <p class="text-sm font-medium text-slate-900">{{ $systemInfo['server_time'] }}</p>
                </div>
            </div>
        </div>

        <!-- Storage Information -->
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Storage</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Storage information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Total Space</span>
                        <span class="text-sm font-medium text-slate-900">{{ $storageInfo['total_space'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Used Space</span>
                        <span class="text-sm font-medium text-slate-900">{{ $storageInfo['used_space'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Free Space</span>
                        <span class="text-sm font-medium text-slate-900">{{ $storageInfo['free_space'] }}</span>
                    </div>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-200">
                    @php
                        $usedPercentage = (disk_total_space(storage_path()) - disk_free_space(storage_path())) / disk_total_space(storage_path()) * 100;
                    @endphp
                    <div class="h-2 rounded-full bg-primary-600" style="width: {{ $usedPercentage }}%"></div>
                </div>
                <p class="text-center text-xs text-slate-500">{{ number_format($usedPercentage, 1) }}% used</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Database Information -->
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Database</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Database information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Connection</span>
                        <span class="text-sm font-medium text-slate-900">{{ $databaseInfo['connection'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Driver</span>
                        <span class="text-sm font-medium text-slate-900">{{ $databaseInfo['driver'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Information -->
        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Cache</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Cache information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Driver</span>
                        <span class="text-sm font-medium text-slate-900">{{ $cacheInfo['driver'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-500">Status</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                            {{ $cacheInfo['status'] === 'Working' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $cacheInfo['status'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Templates -->
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Messaging</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Email templates</h3>
                <p class="mt-1 text-sm text-slate-500">Customize all 8 student notification emails.</p>
            </div>
            <a href="{{ route('admin.settings.email-templates.index') }}" 
               class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                <i class="fas fa-edit text-xs"></i> Manage templates
            </a>
        </div>
        <div class="p-6">
            <p class="mb-3 text-sm text-slate-600">Registration, Self-Registration, Account Approved, Enrollment, Payment Approved, Fully Paid, Assessment Result, and Certificate Issued.</p>
            <a href="{{ route('admin.settings.email-templates.index') }}" class="text-sm font-medium text-primary-700 hover:text-primary-800">Open email templates</a>
        </div>
    </div>

    <!-- Mail Configuration -->
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Mail</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Mail configuration</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-slate-500">Driver</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['driver'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Host</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['host'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Port</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['port'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Encryption</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['encryption'] ?: 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">From Address</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['from_address'] ?: 'Not configured' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">From Name</p>
                    <p class="text-sm font-medium text-slate-900">{{ $mailInfo['from_name'] ?: 'Not configured' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Actions -->
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Actions</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">System actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100"
                            onclick="return confirm('Clear all caches? This may temporarily slow down the application.')">
                        <i class="fas fa-broom text-xs"></i>
                        Clear cache
                    </button>
                </form>

                <form action="{{ route('admin.settings.optimize') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100"
                            onclick="return confirm('Optimize application? This will cache configurations for better performance.')">
                        <i class="fas fa-rocket text-xs"></i>
                        Optimize app
                    </button>
                </form>

                <form action="{{ route('admin.settings.backup-database') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:border-amber-300 hover:bg-amber-100"
                            onclick="return confirm('Create database backup?')">
                        <i class="fas fa-database text-xs"></i>
                        Backup DB
                    </button>
                </form>

                <a href="{{ route('admin.settings.export-data') }}" 
                   class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-medium text-violet-700 transition hover:border-violet-300 hover:bg-violet-100"
                   onclick="return confirm('Export all data as JSON?')">
                    <i class="fas fa-download text-xs"></i>
                    Export data
                </a>
            </div>
        </div>
    </div>

    <!-- General Settings Form -->
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">General</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">General settings</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.settings.update-general') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-slate-700 mb-2">
                            Application Name
                        </label>
                        <input type="text" name="app_name" id="app_name" 
                               class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100" 
                               value="{{ $systemInfo['app_name'] }}" required>
                    </div>
                    
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-slate-700 mb-2">
                            Timezone
                        </label>
                        <select name="timezone" id="timezone" 
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100" 
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
                    <label for="debug_mode" class="ml-2 block text-sm text-slate-900">
                        Enable Debug Mode
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-save text-xs"></i>
                        Update settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Centre</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Centre and time</h3>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div>
                <p class="text-slate-500">Training partner</p>
                <p class="font-medium text-slate-900">{{ $trainingPartnerName ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Timezone</p>
                <p class="font-medium text-slate-900">{{ $systemInfo['timezone'] }}</p>
            </div>
            <div>
                <p class="text-slate-500">Server time</p>
                <p class="font-medium text-slate-900">{{ $systemInfo['server_time'] }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
