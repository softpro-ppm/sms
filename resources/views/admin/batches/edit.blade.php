@extends('layouts.admin')

@section('title', 'Edit Batch')
@section('page-title', 'Edit Batch')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Edit Batch</h2>
                    <p class="text-primary-100 text-sm">Update batch information</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Course Selection -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Course <span class="text-red-500">*</span>
                </label>
                <select id="course_id" 
                        name="course_id" 
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_id') border-red-500 @enderror">
                    <option value="">Choose a course...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" 
                                data-duration="{{ $course->duration_days }}"
                                {{ (old('course_id', $batch->course_id) == $course->id) ? 'selected' : '' }}>
                            {{ $course->name }} - ₹{{ number_format($course->total_fee) }} ({{ $course->duration_days ?? 'N/A' }} days)
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Batch Name -->
            <div>
                <label for="batch_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Batch Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="batch_name" 
                       name="batch_name" 
                       value="{{ old('batch_name', $batch->batch_name) }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('batch_name') border-red-500 @enderror"
                       placeholder="e.g., MSO-1, TALLY-2, etc.">
                <p class="mt-1 text-xs text-gray-500">Use descriptive names like MSO-1, TALLY-2, etc.</p>
                @error('batch_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Schedule Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Batch Schedule</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $batch->start_date->format('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date', $batch->end_date->format('Y-m-d')) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Duration Display -->
                <div class="mt-4 p-4 bg-primary-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-primary-700">Total Duration:</span>
                            <span class="text-lg font-bold text-primary-900 ml-2" id="total-duration-display">{{ \App\Services\BatchDurationService::calculateTotalDays($batch->start_date, $batch->end_date) }} days</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-primary-700">Working Days:</span>
                            <span class="text-lg font-bold text-primary-900 ml-2" id="working-days-display">-</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-primary-700">Sundays:</span>
                            <span class="text-lg font-bold text-primary-900 ml-2" id="sundays-display">-</span>
                        </div>
                    </div>
                    
                    <!-- Accuracy indicator -->
                    <div id="accuracy-indicator" class="mt-3 hidden">
                        <div class="flex items-center">
                            <i id="accuracy-icon" class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span id="accuracy-text" class="text-sm font-medium text-green-800"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacity Section -->
            <div>
                <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">
                    Maximum Students
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-users text-gray-400"></i>
                    </div>
                    <input type="number" 
                           id="max_students" 
                           name="max_students" 
                           value="{{ old('max_students', $batch->max_students) }}"
                           min="1"
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('max_students') border-red-500 @enderror"
                           placeholder="Leave empty for unlimited">
                </div>
                <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited capacity</p>
                @error('max_students')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $batch->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active Batch
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Uncheck to deactivate this batch</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.batches.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Batch
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let courseDuration = null;
    
    // Course selection handler
    document.getElementById('course_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        courseDuration = selectedOption.getAttribute('data-duration');
        
        // Auto-calculate if start date is set
        if (document.getElementById('start_date').value) {
            autoCalculateEndDate();
        }
        
        // Calculate duration
        calculateDuration();
    });
    
    // Auto-calculate end date function
    // Course duration = total days (working + sundays). End = Start + (duration - 1) days.
    function autoCalculateEndDate() {
        const startDate = document.getElementById('start_date').value;
        
        if (!startDate || !courseDuration) {
            return;
        }
        
        const [y, m, d] = startDate.split('-').map(Number);
        const endDateObj = new Date(y, m - 1, d + parseInt(courseDuration) - 1);
        const endDate = endDateObj.getFullYear() + '-' + String(endDateObj.getMonth() + 1).padStart(2, '0') + '-' + String(endDateObj.getDate()).padStart(2, '0');
        document.getElementById('end_date').value = endDate;
        
        // Update duration display
        calculateDuration();
    }
    
    // Calculate duration dynamically
    function calculateDuration() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            // Calculate working days (excluding Sundays) and sundays
            let workingDays = 0;
            let sundays = 0;
            const currentDate = new Date(start);
            
            while (currentDate <= end) {
                if (currentDate.getDay() === 0) {
                    sundays++;
                } else {
                    workingDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            // Total duration = working days + sundays
            const totalDays = workingDays + sundays;
            
            // Update displays
            document.getElementById('total-duration-display').textContent = totalDays + ' days';
            document.getElementById('working-days-display').textContent = workingDays + ' days';
            document.getElementById('sundays-display').textContent = sundays + ' days';
            
            // Show accuracy indicator if course duration is set
            if (courseDuration) {
                const accuracyIndicator = document.getElementById('accuracy-indicator');
                const accuracyIcon = document.getElementById('accuracy-icon');
                const accuracyText = document.getElementById('accuracy-text');
                
                if (totalDays === parseInt(courseDuration)) {
                    accuracyIcon.className = 'fas fa-check-circle text-green-600 mr-2';
                    accuracyText.textContent = 'Perfect! Total duration matches course duration.';
                    accuracyText.className = 'text-sm font-medium text-green-800';
                } else {
                    const difference = totalDays - parseInt(courseDuration);
                    accuracyIcon.className = 'fas fa-exclamation-triangle text-orange-600 mr-2';
                    accuracyText.textContent = `Total duration ${difference > 0 ? 'exceeds' : 'falls short'} by ${Math.abs(difference)} day(s).`;
                    accuracyText.className = 'text-sm font-medium text-orange-800';
                }
                accuracyIndicator.classList.remove('hidden');
            }
        } else {
            document.getElementById('total-duration-display').textContent = 'Select dates to see duration';
            document.getElementById('working-days-display').textContent = '-';
            document.getElementById('sundays-display').textContent = '-';
            document.getElementById('accuracy-indicator').classList.add('hidden');
        }
    }
    
    // Add event listeners
    document.getElementById('start_date').addEventListener('change', function() {
        const endDate = document.getElementById('end_date');
        if (this.value) {
            endDate.min = this.value;
        }
        calculateDuration();
    });
    
    document.getElementById('end_date').addEventListener('change', calculateDuration);
    
    // Calculate on page load
    calculateDuration();
    
    // Initialize course duration if pre-selected
    const courseSelect = document.getElementById('course_id');
    if (courseSelect.value) {
        courseSelect.dispatchEvent(new Event('change'));
    }
</script>
@endsection
