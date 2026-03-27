@extends('layouts.admin')

@section('title', $trainingPartner->name . ' — Activity')
@section('page-title', 'Partner activity')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $trainingPartner->name }}</h2>
            <p class="text-gray-600 mt-1">{{ $trainingPartner->code ?: '—' }} • Full activity (read-only)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($canImpersonate)
            <form method="POST" action="{{ route('admin.super.training-partners.impersonate', $trainingPartner) }}" class="inline"
                  onsubmit="return confirm('Open this centre as its admin? You will see their dashboard and catalogue.');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 text-amber-950 rounded-lg hover:bg-amber-400 font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Open as centre admin
                </button>
            </form>
            @endif
            <a href="{{ route('admin.super.training-partners.show', $trainingPartner) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-building mr-2"></i>Partner profile
            </a>
            <a href="{{ route('admin.super.training-partners.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>All partners
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['students_total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['students_approved'] }} approved · {{ $stats['students_pending'] }} pending</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Batches</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['batches_with_activity'] }}</p>
            <p class="text-xs text-gray-500 mt-1">With enrollments from this partner</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Active enrollments</p>
            <p class="text-2xl font-bold text-teal-600">{{ $stats['active_enrollments'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Fees collected</p>
            <p class="text-2xl font-bold text-green-600">₹{{ number_format($stats['payments_approved_sum'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['payments_approved_count'] }} approved · {{ $stats['payments_pending_count'] }} pending</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Exams & certs</p>
            <p class="text-lg font-bold text-purple-600">{{ $stats['assessment_results'] }} <span class="text-sm font-normal text-gray-500">results</span></p>
            <p class="text-lg font-bold text-amber-600">{{ $stats['certificates_issued'] }} <span class="text-sm font-normal text-gray-500">issued</span></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent batches</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentBatches as $batch)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $batch->batch_name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $batch->course?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $batch->start_date?->format('M j, Y') }} – {{ $batch->end_date?->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">No batches with enrollments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent students</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Added</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentStudents as $student)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $student->full_name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $student->email }}</td>
                        <td class="px-4 py-2"><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $student->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($student->status) }}</span></td>
                        <td class="px-4 py-2 text-gray-500">{{ $student->created_at?->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No students yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent payments</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentPayments as $payment)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $payment->student?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-900">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-2"><span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($payment->status) }}</span></td>
                        <td class="px-4 py-2 text-gray-500">{{ $payment->created_at?->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent exam results</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assessment</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">%</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pass</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentResults as $result)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $result->student?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $result->assessment?->title ?? $result->assessment?->course?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ number_format((float) $result->percentage, 1) }}</td>
                        <td class="px-4 py-2">{{ $result->is_passed ? 'Yes' : 'No' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No assessment results yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 font-semibold text-gray-900">Recent certificates</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Issued</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentCertificates as $cert)
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $cert->student?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $cert->course?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-600 font-mono text-xs">{{ $cert->certificate_number }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $cert->issue_date?->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No certificates yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
