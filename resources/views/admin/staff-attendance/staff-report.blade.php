@extends('layouts.admin')

@section('title', 'Staff Attendance Report')
@section('page-title', 'Staff Attendance Report')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('admin.staff-members.show', $staffMember) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                    <i class="fas fa-arrow-left text-xs"></i> Back to staff profile
                </a>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-chart-line text-[10px]"></i>
                    Individual report
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">{{ $staffMember->name }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $staffMember->staff_code ?: 'No staff ID' }}{{ $staffMember->designation ? ' · ' . $staffMember->designation : '' }}</p>
            </div>
            <form method="GET" action="{{ route('admin.staff-attendance.staff-report', $staffMember) }}" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                <div>
                    <label for="from" class="block text-sm font-medium text-slate-700">From</label>
                    <input type="date" id="from" name="from" value="{{ request('from', $from->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
                </div>
                <div>
                    <label for="to" class="block text-sm font-medium text-slate-700">To</label>
                    <input type="date" id="to" name="to" value="{{ request('to', $to->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-filter text-xs"></i>
                    Filter
                </button>
            </form>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        @foreach([
            ['On-time punch %', $metrics['on_time_percent'] . '%', 'text-emerald-700'],
            ['Late punch %', $metrics['late_percent'] . '%', 'text-amber-700'],
            ['Missing out %', $metrics['missing_checkout_percent'] . '%', 'text-rose-700'],
            ['Total hours', number_format($metrics['total_hours'], 2), 'text-slate-900'],
        ] as [$label, $value, $class])
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight {{ $class }}">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        @foreach([
            ['Present days', $metrics['present']],
            ['Late days', $metrics['late']],
            ['Early check-outs', $metrics['early_checkout']],
            ['Outside location', $metrics['outside_location']],
        ] as [$label, $value])
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Punch quality</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Status split</h3>
            </div>
            <div class="p-6">
                <canvas id="statusChart" class="max-h-[320px]"></canvas>
            </div>
        </section>

        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Daily trend</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Worked hours by day</h3>
            </div>
            <div class="p-6">
                <canvas id="hoursChart" class="max-h-[320px]"></canvas>
            </div>
        </section>
    </div>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Details</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Attendance records</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check in</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check out</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Hours</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Match</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($records as $record)
                        @php
                            $hours = $record->check_in_at && $record->check_out_at ? round($record->check_in_at->diffInMinutes($record->check_out_at) / 60, 2) : null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-900">{{ $record->attendance_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                {{ $record->check_in_at?->format('h:i A') ?? '-' }}
                                @if($record->check_in_status)
                                    <span class="block text-xs {{ $record->check_in_status === 'late' ? 'text-amber-700' : 'text-emerald-700' }}">{{ str_replace('_', ' ', ucfirst($record->check_in_status)) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                {{ $record->check_out_at?->format('h:i A') ?? '-' }}
                                @if($record->check_out_status)
                                    <span class="block text-xs {{ $record->check_out_status === 'early' ? 'text-amber-700' : 'text-emerald-700' }}">{{ str_replace('_', ' ', ucfirst($record->check_out_status)) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $hours !== null ? number_format($hours, 2) : '-' }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                In {{ $record->check_in_match_distance ?? '-' }}<br>
                                Out {{ $record->check_out_match_distance ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                In {{ $record->check_in_distance_meters !== null ? $record->check_in_distance_meters . 'm' : '-' }}<br>
                                Out {{ $record->check_out_distance_meters !== null ? $record->check_out_distance_meters . 'm' : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500">No attendance records found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chart = @json($chart);
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: chart.status_labels,
            datasets: [{
                data: chart.status_values,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                borderWidth: 0,
            }],
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
            cutout: '62%',
        },
    });

    new Chart(document.getElementById('hoursChart'), {
        type: 'bar',
        data: {
            labels: chart.daily_labels,
            datasets: [{
                label: 'Hours',
                data: chart.daily_hours,
                backgroundColor: '#0ea5e9',
                borderRadius: 8,
            }, {
                label: 'Late punch',
                data: chart.daily_late,
                type: 'line',
                borderColor: '#f59e0b',
                backgroundColor: '#f59e0b',
                tension: 0.35,
                yAxisID: 'late',
            }],
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Hours' } },
                late: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { stepSize: 1 }, title: { display: true, text: 'Late' } },
            },
            plugins: { legend: { position: 'bottom' } },
        },
    });
});
</script>
@endsection
