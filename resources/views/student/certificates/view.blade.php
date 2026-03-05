@extends('layouts.student')

@section('title', 'View Certificate')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Certificate Details</h2>
                <p class="text-gray-600 mt-1">Certificate #{{ $certificate->certificate_number }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('student.certificates.download', $certificate) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </a>
                <a href="{{ route('student.certificates') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Certificates
                </a>
            </div>
        </div>
    </div>

    <!-- Certificate Preview (Training Certification format) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Certificate Preview</h3>
        <div class="rounded-lg border border-gray-200 overflow-hidden" style="max-height: 850px;">
            <iframe src="{{ route('student.certificates.preview', $certificate) }}" 
                    class="w-full border-0" 
                    style="height: 800px; min-height: 650px;"
                    title="Certificate Preview"></iframe>
        </div>
    </div>

    <!-- Certificate Information -->
    <div class="bg-blue-50 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">Certificate Information</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>This certificate is issued upon successful completion of the course and assessment.</li>
                        <li>The certificate can be downloaded as an HTML file and printed.</li>
                        <li>For verification purposes, please keep your certificate number safe.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
