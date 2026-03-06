@extends('layouts.admin')

@section('title', 'Enroll Students')
@section('page-title', 'Enroll Students')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Add/Enroll Students</h2>
            <p class="text-gray-600 mt-1">{{ $batch->batch_name }} • {{ $batch->course->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.batches.show', $batch) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Batch
            </a>
        </div>
    </div>

    <!-- Fee Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>Enrollment Fee Structure
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-600">Registration:</span> <span class="font-semibold">₹{{ number_format($registrationFee, 0) }}</span></div>
            <div><span class="text-gray-600">Course Fee:</span> <span class="font-semibold">₹{{ number_format($courseFee, 0) }}</span></div>
            <div><span class="text-gray-600">Assessment:</span> <span class="font-semibold">₹{{ number_format($assessmentFee, 0) }}</span></div>
            <div><span class="text-gray-600">Total per student:</span> <span class="font-semibold text-green-600">₹{{ number_format($totalFee, 0) }}</span></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Only approved students with no enrollments in any batch are shown below.</p>
    </div>

    <!-- Search Form (outside enroll form to avoid nested forms) -->
    <div class="bg-white rounded-xl shadow-lg p-4">
        <form method="GET" action="{{ route('admin.batches.enroll', $batch) }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, email, aadhar..."
                       class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 w-full">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i>Search
            </button>
        </form>
    </div>

    <!-- Enroll Form -->
    <form action="{{ route('admin.batches.enroll.store', $batch) }}" method="POST" id="enrollForm">
        @csrf
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-900">Eligible Students ({{ $students->total() }})</h3>
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <span>Enrollment Date:</span>
                        <input type="date" name="enrollment_date" value="{{ date('Y-m-d') }}" required
                               class="px-3 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </label>
                </div>
            </div>

            @if($students->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Select all on page</span>
                    </label>
                    <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                </div>
                <button type="submit" id="enrollBtn" disabled
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                    <i class="fas fa-user-plus mr-2"></i>
                    Enroll Selected Students
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" title="Select all">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" 
                                       class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-white font-semibold text-sm">{{ substr($student->full_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                        <div class="text-sm text-gray-500">Aadhar: {{ $student->aadhar_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                <div class="text-sm text-gray-500">{{ $student->whatsapp_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(($student->credit_balance ?? 0) > 0)
                                    <span class="text-sm font-medium text-green-600">₹{{ number_format($student->credit_balance, 0) }}</span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Rows per page:</label>
                    <select id="perPageSelect" class="px-2 py-1 border border-gray-300 rounded-md text-sm">
                        @foreach([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    {{ $students->withQueryString()->links() }}
                </div>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-users-slash text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No eligible students</p>
                    <p class="text-sm mt-1">All approved students are either already enrolled in a batch or there are no approved students yet.</p>
                    <p class="text-xs text-gray-400 mt-2">Only approved students with zero enrollments in any batch are shown here.</p>
                    <a href="{{ route('admin.batches.show', $batch) }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Batch
                    </a>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

@if($students->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location = url.toString();
        });
    }

    const form = document.getElementById('enrollForm');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const enrollBtn = document.getElementById('enrollBtn');
    const selectedCountEl = document.getElementById('selectedCount');

    function updateState() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        const count = checked.length;
        selectedCountEl.textContent = count + ' selected';
        enrollBtn.disabled = count === 0;
        selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
        if (selectAllHeader) selectAllHeader.checked = selectAll.checked;
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateState));

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateState();
        });
    }
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            selectAll.checked = this.checked;
            updateState();
        });
    }

    form.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one student to enroll.');
            return false;
        }
    });

    updateState();
});
</script>
@endif
@endsection
