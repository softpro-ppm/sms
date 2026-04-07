@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Platform Overview</h2>
                <p class="text-purple-100 text-lg">Manage training partners and platform settings.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-shield-alt text-purple-600 text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Partners</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_partners'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Partners</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_partners'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-amber-100">
            <p class="text-sm font-medium text-gray-600">Pending partner applications</p>
            <p class="text-3xl font-bold text-amber-600">{{ $stats['pending_partners'] }}</p>
            <a href="{{ route('admin.super.training-partners.index', ['status' => 'pending']) }}" class="text-xs text-primary-600 hover:underline mt-2 inline-block">Review →</a>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-orange-100">
            <p class="text-sm font-medium text-gray-600">Students awaiting approval</p>
            <p class="text-3xl font-bold text-orange-600">{{ $stats['students_pending'] }}</p>
            <p class="text-xs text-gray-500 mt-2">Across all centres</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-100">
            <p class="text-sm font-medium text-gray-600">Payments pending approval</p>
            <p class="text-3xl font-bold text-yellow-700">{{ $stats['pending_payments_all'] }}</p>
            <p class="text-xs text-gray-500 mt-2">All centres — centre admin approves</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">HQ Partners</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['hq_partners'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Standard Partners</p>
                    <p class="text-2xl font-bold text-teal-600">{{ $stats['standard_partners'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Staff</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['total_staff'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations & content health -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-amber-100">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-amber-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Pending partners</h3>
                    <a href="{{ route('admin.super.training-partners.index', ['status' => 'pending']) }}" class="text-sm text-primary-600 hover:text-primary-800">View all →</a>
                </div>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"></th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($pendingPartners as $p)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $p->type }}</td>
                                <td class="px-4 py-2"><a href="{{ route('admin.super.training-partners.show', $p) }}" class="text-primary-600 hover:underline">Open</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No pending registrations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-red-100">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-red-50/30">
                    <h3 class="text-lg font-semibold text-gray-900">Low wallet (Standard &lt; ₹{{ number_format($lowWalletThreshold, 0) }})</h3>
                    <a href="{{ route('admin.super.training-partners.index', ['type' => 'STANDARD', 'status' => 'active']) }}" class="text-sm text-primary-600 hover:text-primary-800">Partners →</a>
                </div>
                <div class="overflow-x-auto max-h-56 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 sticky top-0"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"></th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($lowWalletPartners as $p)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-4 py-2 text-red-700 font-semibold">₹{{ number_format($p->wallet_balance, 2) }}</td>
                                <td class="px-4 py-2"><a href="{{ route('admin.super.training-partners.show', $p) }}" class="text-primary-600 hover:underline">Recharge</a></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No partners below threshold.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Failed WhatsApp sends</h3>
                    <a href="{{ route('admin.whatsapp-logs.index') }}" class="text-sm text-primary-600 hover:text-primary-800">All logs →</a>
                </div>
                <div class="overflow-x-auto max-h-56 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">When</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentFailedWhatsApp as $log)
                            <tr>
                                <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $log->created_at?->diffForHumans() }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $log->template_name }}</td>
                                <td class="px-4 py-2 text-red-700 text-xs max-w-xs truncate" title="{{ $log->error_message }}">{{ \Illuminate\Support\Str::limit($log->error_message ?? '—', 80) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No failed WhatsApp rows (or table not migrated).</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Queue failures</h3>
                    <span class="text-sm font-medium {{ $failedJobsCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $failedJobsCount }} total</span>
                </div>
                <p class="px-6 py-2 text-xs text-gray-500">Requires <code class="bg-gray-100 px-1 rounded">QUEUE_CONNECTION=database</code> (or redis) and <code class="bg-gray-100 px-1 rounded">php artisan queue:work</code>.</p>
                <div class="overflow-x-auto max-h-48 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Failed at</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Exception</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentFailedJobs as $job)
                            <tr>
                                <td class="px-4 py-2 text-gray-500 whitespace-nowrap text-xs">{{ $job->failed_at }}</td>
                                <td class="px-4 py-2 text-red-800 text-xs font-mono">{{ \Illuminate\Support\Str::limit($job->exception ?? '', 120) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-6 text-center text-gray-500">No failed_jobs rows.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">After deploy</h3>
                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                    <li><code class="bg-white px-1 rounded border">php artisan migrate --force</code></li>
                    <li><code class="bg-white px-1 rounded border">php artisan config:cache</code> / <code class="bg-white px-1 rounded border">route:cache</code> as needed</li>
                    <li>Cron: <code class="bg-white px-1 rounded border">* * * * * php artisan schedule:run</code> (backups at 03:00 server time)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Partners -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Training Partners</h3>
            <a href="{{ route('admin.super.training-partners.index') }}"
               class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPartners as $partner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $partner->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $partner->code ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $partner->type === 'HQ' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $partner->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $partner->status === 'active' ? 'bg-green-100 text-green-800' : ($partner->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.super.training-partners.show', $partner) }}"
                               class="text-primary-600 hover:text-primary-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No training partners yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
