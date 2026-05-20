@extends('layouts.admin')

@section('title', 'Email Templates')
@section('page-title', 'Email Templates')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-envelope-open-text text-[10px]"></i>
                    Email templates
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Manage student-facing notification templates.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review default and customized emails used for registration, approvals, payments, results, and certificates.</p>
            </div>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                <i class="fas fa-arrow-left text-xs"></i>
                Back to settings
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Template records</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Available email templates</h3>
            </div>
            <div class="text-sm text-slate-500">{{ count($templates) }} templates</div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">#</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Template</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Subject</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                    <th class="px-5 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($templates as $index => $template)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="font-medium text-slate-900">{{ $template->name }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-sm text-slate-600">{{ Str::limit($template->subject, 50) }}</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        @if($template->header_html || $template->body_html)
                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Customized</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Default</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-right">
                        <a href="{{ route('admin.settings.email-templates.edit', $template) }}" 
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600 transition hover:border-blue-300 hover:bg-blue-100"
                           title="Edit template">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
