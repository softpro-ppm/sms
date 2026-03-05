@extends('layouts.admin')

@section('title', 'Record Payment')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.payments.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Record Payment</h1>
                <p class="text-gray-600 mt-2">Record a new payment for a student</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.payments.store') }}" class="space-y-8">
            @csrf
            
            <!-- Student Selection Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">Student Selection</h2>
                            <p class="text-blue-100 text-sm">Select the student for whom you're recording payment</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Search -->
                        <div>
                            <label for="student_search" class="block text-sm font-medium text-gray-700 mb-2">
                                Search Student <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="student_search" 
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Search by name, email, or Aadhar..."
                                       onkeyup="searchStudents(this.value)">
                                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                            </div>
                            <div id="student_results" class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg hidden">
                                <!-- Student search results will appear here -->
                            </div>
                        </div>

                        <!-- Selected Student Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Selected Student
                            </label>
                            <div id="selected_student" class="p-4 border border-gray-300 rounded-lg bg-gray-50 min-h-[60px] flex items-center">
                                <span class="text-gray-500">No student selected</span>
                            </div>
                            <input type="hidden" id="student_id" name="student_id" value="{{ old('student_id') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-credit-card text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">Payment Details</h2>
                            <p class="text-green-100 text-sm">Enter payment amount and details</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Enrollment Selection -->
                        <div>
                            <label for="enrollment_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Course & Batch <span class="text-red-500">*</span>
                            </label>
                            <select id="enrollment_id" 
                                    name="enrollment_id" 
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('enrollment_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select Course & Batch</option>
                                <!-- Options will be populated dynamically -->
                            </select>
                            
                            <!-- Pre-select enrollment if provided -->
                            @if(isset($selectedEnrollment))
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        window.preSelectedEnrollment = {
                                            id: {{ $selectedEnrollment->id }},
                                            courseName: "{{ $selectedEnrollment->batch->course->name }}",
                                            batchName: "{{ $selectedEnrollment->batch->batch_name }}",
                                            totalFee: {{ $selectedEnrollment->total_fee }},
                                            outstandingAmount: {{ $selectedEnrollment->outstanding_amount ?? ($selectedEnrollment->total_fee - $selectedEnrollment->paid_amount) }}
                                        };
                                    });
                                </script>
                            @endif
                            @error('enrollment_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Breakdown (Hidden initially, shown when enrollment is selected) -->
                        <div id="fee-breakdown" class="col-span-1 md:col-span-2 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-blue-900 mb-3">Fee Breakdown</h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="text-center">
                                        <div class="text-sm text-blue-600 font-medium">Registration Fee</div>
                                        <div class="text-lg font-bold text-blue-900" id="reg-fee">₹100</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-blue-600 font-medium">Course Fee</div>
                                        <div class="text-lg font-bold text-blue-900" id="course-fee">₹0</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-blue-600 font-medium">Exam Fee</div>
                                        <div class="text-lg font-bold text-blue-900" id="assessment-fee">₹100</div>
                                    </div>
                                    <div class="text-center bg-blue-100 rounded-lg p-2">
                                        <div class="text-sm text-blue-600 font-medium">Total Fee</div>
                                        <div class="text-xl font-bold text-blue-900" id="total-fee">₹0</div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-blue-600 font-medium">Outstanding Amount:</span>
                                        <span class="text-lg font-bold text-blue-900" id="outstanding-amount">₹0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Type (Hidden - Auto-allocated) -->
                        <input type="hidden" name="payment_type" value="partial">
                        
                        <!-- Payment Information -->
                        <div class="col-span-1 md:col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-blue-900 mb-2">Payment Information</h4>
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Payments are automatically allocated to fee types in order: Registration Fee → Course Fee → Exam Fee
                                </p>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">₹</span>
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}"
                                       step="0.01"
                                       min="0.01"
                                       class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('amount') border-red-500 @enderror"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remarks -->
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                Remarks
                            </label>
                            <textarea id="remarks" 
                                      name="remarks" 
                                      rows="3"
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('remarks') border-red-500 @enderror"
                                      placeholder="Optional remarks about this payment...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Payment Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4">
                        <div class="text-sm text-gray-600">Receipt Number</div>
                        <div class="text-lg font-semibold text-gray-900" id="receipt_preview">RCP-2025-XXXXXX</div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="text-sm text-gray-600">Payment Status</div>
                        <div class="text-lg font-semibold text-orange-600">Pending Approval</div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <div class="text-sm text-gray-600">Recorded By</div>
                        <div class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.payments.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Auto-dismissing notifications -->
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    let students = [];
    let selectedStudent = null;

    // Load students on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStudents();
        
        // If there's a pre-selected student from URL, select them immediately
        const urlParams = new URLSearchParams(window.location.search);
        const studentId = urlParams.get('student_id');
        const enrollmentId = urlParams.get('enrollment_id');
        console.log('URL params:', { studentId, enrollmentId });
        
        if (studentId) {
            // Wait for students to load, then auto-select
            setTimeout(() => {
                console.log('Attempting to select student:', studentId);
                console.log('Students loaded:', students.length);
                selectStudent(studentId);
            }, 2000); // Increased timeout
        }
    });

    // Load all students
    function loadStudents() {
        console.log('Loading students...');
        fetch('/admin/api/students')
            .then(response => {
                console.log('Student API response status:', response.status);
                return response.json();
            })
            .then(data => {
                students = data;
                console.log('Loaded students:', students.length, data);
            })
            .catch(error => {
                console.error('Error loading students:', error);
            });
    }

    // Search students
    function searchStudents(query) {
        const resultsDiv = document.getElementById('student_results');
        
        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        const filteredStudents = students.filter(student => 
            student.full_name.toLowerCase().includes(query.toLowerCase()) ||
            student.email.toLowerCase().includes(query.toLowerCase()) ||
            student.aadhar_number.includes(query)
        );

        if (filteredStudents.length === 0) {
            resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No students found</div>';
        } else {
            resultsDiv.innerHTML = filteredStudents.map(student => `
                <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                     onclick="selectStudent(${student.id})">
                    <div class="font-medium">${student.full_name}</div>
                    <div class="text-sm text-gray-500">${student.email}</div>
                    <div class="text-xs text-gray-400">Aadhar: ${student.aadhar_number}</div>
                </div>
            `).join('');
        }

        resultsDiv.classList.remove('hidden');
    }

    // Select student
    function selectStudent(studentId) {
        console.log('selectStudent called with:', studentId);
        console.log('Available students:', students);
        
        const student = students.find(s => s.id == studentId); // Use == for type conversion
        console.log('Found student:', student);
        
        if (student) {
            selectedStudent = student;
            document.getElementById('student_id').value = studentId;
            document.getElementById('selected_student').innerHTML = `
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">${student.full_name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <div class="font-medium">${student.full_name}</div>
                        <div class="text-sm text-gray-500">${student.email}</div>
                    </div>
                </div>
            `;
            document.getElementById('student_results').classList.add('hidden');
            document.getElementById('student_search').value = '';
            
            // Load enrollments for this student
            loadStudentEnrollments(studentId);
        }
    }

    // Load student enrollments
    function loadStudentEnrollments(studentId) {
        fetch(`/admin/api/students/${studentId}/enrollments`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('enrollment_id');
                select.innerHTML = '<option value="">Select Course & Batch</option>';
                
                data.forEach(enrollment => {
                    const option = document.createElement('option');
                    option.value = enrollment.id;
                    option.textContent = `${enrollment.batch.course.name} - ${enrollment.batch.batch_name}`;
                    // Store enrollment data for fee breakdown
                    option.dataset.registrationFee = enrollment.registration_fee || 100;
                    option.dataset.courseFee = enrollment.course_fee || 0;
                    option.dataset.assessmentFee = enrollment.assessment_fee || 100;
                    option.dataset.totalFee = enrollment.total_fee || 0;
                    option.dataset.paidAmount = enrollment.paid_amount || 0;
                    option.dataset.outstandingAmount = enrollment.outstanding_amount || 0;
                    select.appendChild(option);
                });
                
                // If there's a pre-selected enrollment, select it
                if (window.preSelectedEnrollment) {
                    select.value = window.preSelectedEnrollment.id;
                    // Trigger the fee breakdown display
                    updateFeeBreakdown(window.preSelectedEnrollment);
                }
            })
            .catch(error => {
                console.error('Error loading enrollments:', error);
            });
    }
    
    // Helper function to update fee breakdown
    function updateFeeBreakdown(enrollmentData) {
        const feeBreakdown = document.getElementById('fee-breakdown');
        feeBreakdown.classList.remove('hidden');
        
        document.getElementById('reg-fee').textContent = `₹100`;
        document.getElementById('course-fee').textContent = `₹${enrollmentData.totalFee - 200}`;
        document.getElementById('assessment-fee').textContent = `₹100`;
        document.getElementById('total-fee').textContent = `₹${enrollmentData.totalFee}`;
        document.getElementById('outstanding-amount').textContent = `₹${enrollmentData.outstandingAmount}`;
        
        // Set max amount for partial payments
        const amountInput = document.getElementById('amount');
        amountInput.max = enrollmentData.outstandingAmount;
    }

    // Handle enrollment selection change
    document.getElementById('enrollment_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const feeBreakdown = document.getElementById('fee-breakdown');
        
        if (this.value && selectedOption.dataset.totalFee) {
            // Show fee breakdown
            feeBreakdown.classList.remove('hidden');
            
            // Update fee breakdown display
            document.getElementById('reg-fee').textContent = `₹${selectedOption.dataset.registrationFee}`;
            document.getElementById('course-fee').textContent = `₹${selectedOption.dataset.courseFee}`;
            document.getElementById('assessment-fee').textContent = `₹${selectedOption.dataset.assessmentFee}`;
            document.getElementById('total-fee').textContent = `₹${selectedOption.dataset.totalFee}`;
            document.getElementById('outstanding-amount').textContent = `₹${selectedOption.dataset.outstandingAmount}`;
            
            // Set max amount for partial payments
            const amountInput = document.getElementById('amount');
            amountInput.max = selectedOption.dataset.outstandingAmount;
        } else {
            // Hide fee breakdown
            feeBreakdown.classList.add('hidden');
        }
    });

    // Generate receipt preview
    function generateReceiptPreview() {
        const now = new Date();
        const year = now.getFullYear();
        const count = Math.floor(Math.random() * 999999) + 1;
        const receiptNumber = `RCP-${year}-${count.toString().padStart(6, '0')}`;
        document.getElementById('receipt_preview').textContent = receiptNumber;
    }

    // Generate receipt preview on page load
    generateReceiptPreview();
</script>
@endsection
