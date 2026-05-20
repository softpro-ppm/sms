@extends('layouts.student')

@section('title', 'View Certificate')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6 sm:px-8 sm:py-7 border-b border-gray-100">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">
                <i class="fas fa-certificate text-[11px]"></i>
                Certificate
            </span>
            <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-950">Certificate Details</h2>
                    <p class="mt-2 text-sm text-gray-600">Certificate #{{ $certificate->certificate_number }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('student.certificates.download', $certificate) }}"
                       class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700">
                        <i class="fas fa-download mr-2 text-xs"></i>
                        Download
                    </a>
                    <a href="{{ route('student.certificates') }}"
                       class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i>
                        Back to certificates
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-4 sm:p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-gray-950">Certificate preview</h3>
                    <span class="text-xs font-medium text-gray-500">Preview only</span>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white" style="max-height: 850px;">
                    <iframe src="{{ route('student.certificates.preview', $certificate) }}"
                            class="w-full border-0"
                            style="height: 800px; min-height: 650px;"
                            title="Certificate Preview"></iframe>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h3 class="text-base font-semibold text-gray-950">Certificate information</h3>
                </div>
                <div class="px-5 py-5">
                    <ul class="space-y-3 text-sm leading-6 text-gray-600">
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check mt-1 text-xs text-emerald-600"></i>
                            <span>This certificate is issued after successful course and assessment completion.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check mt-1 text-xs text-emerald-600"></i>
                            <span>You can download the PDF version for printing or sharing whenever needed.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-circle-check mt-1 text-xs text-emerald-600"></i>
                            <span>Keep the certificate number safe for verification and future reference.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
