@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-shield-alt text-[10px] text-primary-600"></i>
                        Super Admin Dashboard
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">Manage partner approvals, wallet risk, and platform exceptions.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review new training partners, monitor centres that need intervention, and keep queue failures visible from one governance dashboard.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.super.training-partners.index', ['status' => 'pending']) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-user-check"></i>
                        Review partners
                    </a>
                    <a href="{{ route('admin.super.training-partners.index', ['type' => 'STANDARD', 'status' => 'active']) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-wallet"></i>
                        Wallet watch
                    </a>
                    <a href="{{ route('admin.super.impersonation-log.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-user-secret"></i>
                        Impersonation log
                    </a>
                    <a href="{{ route('admin.whatsapp-logs.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp logs
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Active Partners</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['active_partners']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Centres currently active</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Network Students</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['total_students']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Students across all centres</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Pending Centre Approvals</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_partners']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Applications waiting for review</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Centre Staff</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['total_staff']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Admin and reception accounts</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.super.training-partners.index', ['status' => 'pending']) }}" class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Pending Partners</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['pending_partners'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">New centre applications waiting for approval or rejection.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.super.training-partners.index', ['type' => 'STANDARD', 'status' => 'active']) }}" class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-rose-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">Low Wallet Centres</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['low_wallet'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Standard partners below the alert threshold of ₹{{ number_format($superWorkspace['low_wallet_threshold'] ?? 0, 0) }}.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-700">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.super.training-partners.index') }}" class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-400 hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">Inactive Centres</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['inactive_partners'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Suspended centres needing review before they return to service.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.super.training-partners.index') }}" class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Backlog Centres</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['high_backlog'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Centres with approval, payment, or wallet pressure that need support.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.whatsapp-logs.index') }}" class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:border-violet-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-violet-700">Failed WhatsApp</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['failed_whatsapp'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Recent message delivery failures visible from the platform layer.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                    <i class="fab fa-whatsapp"></i>
                </div>
            </div>
        </a>

        <div class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">Queue Failures</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($superWorkspace['queue_counts']['failed_jobs'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Failed jobs currently in the queue backend across all workers.</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Governance Queues</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Partner approvals and partner risk</h3>
                </div>
                <a href="{{ route('admin.super.training-partners.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                    Open partner list
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Pending partner approvals</h4>
                        <a href="{{ route('admin.super.training-partners.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Review all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($superWorkspace['pending_partners'] ?? collect()) as $partner)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $partner->name }}</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ $partner->type }} centre</p>
                                    </div>
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ ucfirst($partner->status) }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Applied {{ $partner->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No pending partner applications.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Low wallet centres</h4>
                        <a href="{{ route('admin.super.training-partners.index', ['type' => 'STANDARD', 'status' => 'active']) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Wallet view</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($superWorkspace['low_wallet_partners'] ?? collect()) as $partner)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $partner->name }}</p>
                                        <p class="mt-1 text-sm text-gray-600">{{ $partner->code ?: 'No code' }}</p>
                                    </div>
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">₹{{ number_format($partner->wallet_balance, 2) }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Recharge before student approvals start failing.</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No low-wallet centres right now.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Centre Attention</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Backlog and intervention ranking</h3>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse(($superWorkspace['high_backlog_partners'] ?? collect()) as $item)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ $item['partner']->name }}</h4>
                                <p class="mt-1 text-sm text-gray-600">{{ $item['partner']->type }} · {{ ucfirst($item['partner']->status) }}</p>
                            </div>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                Score {{ $item['attention_score'] }}
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-600">
                            <div class="rounded-2xl bg-white px-3 py-2">Pending students: <span class="font-semibold text-slate-900">{{ $item['partner']->pending_students_count }}</span></div>
                            <div class="rounded-2xl bg-white px-3 py-2">Pending payments: <span class="font-semibold text-slate-900">{{ $item['pending_payments_count'] }}</span></div>
                            <div class="rounded-2xl bg-white px-3 py-2">Students: <span class="font-semibold text-slate-900">{{ $item['partner']->students_count }}</span></div>
                            <div class="rounded-2xl bg-white px-3 py-2">Staff: <span class="font-semibold text-slate-900">{{ $item['partner']->staff_count }}</span></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No centres need intervention right now.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Platform Alerts</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Messaging and queue failures</h3>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Failed WhatsApp sends</h4>
                        <a href="{{ route('admin.whatsapp-logs.index') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Open logs</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentFailedWhatsApp as $log)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $log->template_name ?: 'Template send' }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $log->student?->full_name ?? 'Student not linked' }}</p>
                                <p class="mt-2 text-xs text-rose-700">{{ \Illuminate\Support\Str::limit($log->error_message ?? 'No error message stored.', 90) }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No recent WhatsApp failures.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Failed jobs</h4>
                        <span class="rounded-full {{ $failedJobsCount > 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }} px-3 py-1 text-xs font-semibold">
                            {{ $failedJobsCount }} total
                        </span>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentFailedJobs as $job)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $job->failed_at }}</p>
                                <p class="mt-2 text-xs font-mono leading-5 text-rose-700">{{ \Illuminate\Support\Str::limit($job->exception ?? 'No exception stored.', 120) }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No failed queue jobs right now.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Recent Centres</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Latest training partner records</h3>
                </div>
                <a href="{{ route('admin.super.training-partners.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                    All partners
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse($recentPartners as $partner)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ $partner->name }}</h4>
                                <p class="mt-1 text-sm text-gray-600">{{ $partner->code ?: 'No code' }} · {{ $partner->type }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $partner->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($partner->status === 'suspended' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Created {{ $partner->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No training partners found.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
