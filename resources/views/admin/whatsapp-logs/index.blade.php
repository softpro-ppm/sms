@extends('layouts.admin')

@section('title', 'WhatsApp Logs')
@section('page-title', 'WhatsApp Logs')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6">
            <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                <i class="fab fa-whatsapp text-[10px]"></i>
                WhatsApp logs
            </div>
            <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review sent notifications and delivery status.</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Use the log to confirm delivery outcomes, template usage, and student message history across WhatsApp sends.</p>
        </div>
    </section>

    @if(isset($tableMissing) && $tableMissing)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
        <p class="text-amber-800">The <code>whatsapp_logs</code> table has not been created yet. Run <code>php artisan migrate</code> to create it.</p>
    </div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Log records</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Latest WhatsApp activity</h3>
            </div>
            <div class="text-sm text-slate-500">{{ method_exists($logs, 'total') ? $logs->total() : count($logs) }} records</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        <th class="px-5 py-2.5 text-left text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-slate-500">Type</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Phone</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Student</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Template</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-5 py-3.5 text-sm text-gray-900">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-900">{{ $log->type }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-900">{{ $log->phone }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-900">{{ $log->student?->full_name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-900">{{ $log->template_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-500">No WhatsApp logs yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </section>
</div>
@endsection
