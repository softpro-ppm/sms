@extends('layouts.student')

@section('title', 'My Certificates')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">My Certificates</h2>
                <p class="text-gray-600 mt-1">View and download your earned certificates.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: {{ $certificates->total() }} certificates</span>
            </div>
        </div>
    </div>

    <!-- Certificates List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($certificates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Certificate Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Issue Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($certificates as $certificate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 flex items-center justify-center">
                                                <i class="fas fa-certificate text-white"></i>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $certificate->course->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $certificate->course->duration_days }} days
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($certificate->batch)
                                        <div class="text-sm text-gray-900">
                                            {{ $certificate->batch->batch_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $certificate->batch->start_date->format('M Y') }} - 
                                            {{ $certificate->batch->end_date->format('M Y') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $certificate->is_issued ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <i class="fas fa-{{ $certificate->is_issued ? 'check-circle' : 'clock' }} mr-1"></i>
                                        {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($certificate->is_issued)
                                        <a href="{{ route('student.certificates.download', $certificate) }}" 
                                           class="text-primary-600 hover:text-primary-900 mr-3">
                                            <i class="fas fa-download mr-1"></i>
                                            Download
                                        </a>
                                        <a href="{{ route('student.certificates.view', $certificate) }}" 
                                           class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-eye mr-1"></i>
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
                <div class="px-6 py-4 border-t border-gray-200">
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
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Certificate Summary -->
    @if($certificates->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Certificate Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 rounded-lg p-4">
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
                
                <div class="bg-yellow-50 rounded-lg p-4">
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
                
                <div class="bg-blue-50 rounded-lg p-4">
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
        </div>
    @endif

    <!-- Certificate Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Certificate Information</h3>
                <div class="mt-2 text-sm text-blue-700">
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
