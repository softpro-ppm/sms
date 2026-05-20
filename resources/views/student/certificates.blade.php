@extends('layouts.student')

@section('title', 'My Certificates')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-certificate text-[10px]"></i>
                    Certificates
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">View issued certificates and pending certificate status.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Download completed certificates, track pending issues, and confirm course completion records.</p>
            </div>
            <div class="text-sm text-slate-500">{{ $certificates->total() }} certificates</div>
        </div>
    </section>

    <!-- Certificates List -->
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @if($certificates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Certificate Details
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Course
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Batch
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Issue Date
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($certificates as $certificate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                                                <i class="fas fa-certificate"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                Certificate #{{ $certificate->certificate_number ?? 'Pending' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $certificate->course->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $certificate->course->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $certificate->course->duration_days }} days
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($certificate->batch)
                                        <div class="text-sm text-gray-900">
                                            {{ $certificate->batch->batch_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                $e = $certificate->enrollment;
                                                $ds = $e?->effective_start_date ?? $certificate->batch->start_date;
                                                $de = $e?->effective_end_date ?? $certificate->batch->end_date;
                                            @endphp
                                            {{ $ds?->format('M Y') ?? '—' }} -
                                            {{ $de?->format('M Y') ?? '—' }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                        {{ $certificate->is_issued ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        <i class="fas fa-{{ $certificate->is_issued ? 'check-circle' : 'clock' }} mr-1"></i>
                                        {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-900">
                                    {{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                                    @if($certificate->is_issued)
                                        <a href="{{ route('student.certificates.download', $certificate) }}" 
                                           class="mr-2 inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                                            <i class="fas fa-download text-xs"></i>
                                            Download
                                        </a>
                                        <a href="{{ route('student.certificates.view', $certificate) }}" 
                                           class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                                            <i class="fas fa-eye text-xs"></i>
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($certificates->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $certificates->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300">
                    <i class="fas fa-certificate text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No certificates earned</h3>
                <p class="mt-2 text-gray-500">You haven't earned any certificates yet.</p>
                <div class="mt-6">
                    <a href="{{ route('student.dashboard') }}" 
                       class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Certificate Summary -->
    @if($certificates->count() > 0)
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Summary</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Certificate summary</h3>
            </div>
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Issued</p>
                            <p class="text-2xl font-bold text-green-900">
                                {{ $certificates->where('is_issued', true)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Pending</p>
                            <p class="text-2xl font-bold text-yellow-900">
                                {{ $certificates->where('is_issued', false)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trophy text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Total</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $certificates->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Certificate Information -->
    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-900">Certificate information</h3>
                <div class="mt-2 text-sm text-blue-800">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Certificates are issued upon successful completion of courses and assessments.</li>
                        <li>Downloaded certificates are in HTML format and can be verified online.</li>
                        <li>If you don't see a certificate you expect, please contact the administration.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
