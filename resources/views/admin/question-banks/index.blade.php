@extends('layouts.admin')

@section('title', 'Question Bank Management')
@section('page-title', 'Question Bank Management')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Question Bank</h2>
            <p class="text-gray-600 mt-1">Manage questions for assessments and tests</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="showBulkUploadModal()" 
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg">
                <i class="fas fa-upload mr-2"></i>
                Bulk Upload
            </button>
            <a href="{{ route('admin.question-banks.export', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-700 to-gray-800 text-white font-medium rounded-lg hover:from-gray-800 hover:to-gray-900 transition-all duration-200 shadow-lg">
                <i class="fas fa-file-export mr-2"></i>
                Export CSV
            </a>
            <a href="{{ route('admin.question-banks.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Add Question
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-question-circle text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $questions->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\QuestionBank::where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $courses->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Subjects</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $subjects->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.question-banks.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select name="course_id" id="course_id" data-live-filter class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <select name="subject" id="subject" data-live-filter class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>
                                    {{ $subject }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                        <select name="difficulty_level" id="difficulty_level" data-live-filter class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Levels</option>
                            <option value="easy" {{ request('difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ request('difficulty_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ request('difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" 
                               data-live-search
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                               placeholder="Search questions..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-600">Rows</label>
                        <select id="per_page" name="per_page" data-live-rows
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @foreach([10,20,50,100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('admin.question-banks.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Questions</h3>
        </div>
        <div class="overflow-hidden">
            @if($questions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($questions as $question)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $question->course->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $question->subject }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                        <div class="truncate">
                                            {{ Str::limit($question->question_text, 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $question->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : 
                                               ($question->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($question->difficulty_level) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $question->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $question->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.question-banks.show', $question) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.question-banks.edit', $question) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.question-banks.toggle-status', $question) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-{{ $question->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $question->is_active ? 'yellow' : 'green' }}-900 p-1 rounded hover:bg-{{ $question->is_active ? 'yellow' : 'green' }}-50">
                                                    <i class="fas fa-{{ $question->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.question-banks.destroy', $question) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to delete this question?')"
                                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $questions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-question-circle text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Questions Found</h3>
                    <p class="mt-1 text-sm text-gray-500">No questions match your current filters.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.question-banks.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add First Question
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulkUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Bulk Upload Questions</h3>
                <button onclick="hideBulkUploadModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.question-banks.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="course_id_upload" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select name="course_id" id="course_id_upload" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" accept=".csv" required>
                        <p class="mt-1 text-sm text-gray-500">
                            Upload a CSV file with questions. 
                            <a href="{{ route('admin.question-banks.download-template') }}" class="text-primary-600 hover:text-primary-700">Download template</a>
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideBulkUploadModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Upload Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const courseSelect = document.getElementById('course_id');
    const subjectSelect = document.getElementById('subject');

    const updateSubjects = (courseId, selectedSubject) => {
        if (!courseId) {
            subjectSelect.innerHTML = '<option value="">All Subjects</option>';
            @foreach($subjects as $subject)
                subjectSelect.insertAdjacentHTML('beforeend', `<option value="{{ $subject }}">{{ $subject }}</option>`);
            @endforeach
            return;
        }

        fetch(`{{ route('admin.question-banks.subjects-by-course') }}?course_id=${courseId}`)
            .then((response) => response.json())
            .then((subjects) => {
                subjectSelect.innerHTML = '<option value="">All Subjects</option>';
                subjects.forEach((subject) => {
                    const selected = selectedSubject && selectedSubject === subject ? 'selected' : '';
                    subjectSelect.insertAdjacentHTML('beforeend', `<option value="${subject}" ${selected}>${subject}</option>`);
                });
            })
            .catch(() => {
                subjectSelect.innerHTML = '<option value="">All Subjects</option>';
            });
    };

    // Auto-submit form when course changes
    courseSelect.addEventListener('change', function() {
        updateSubjects(this.value, '');
        this.form.submit();
    });

    // Sync subjects on page load when course filter is set
    updateSubjects(courseSelect.value, '{{ request('subject') }}');

    // Bulk upload modal functions
    function showBulkUploadModal() {
        document.getElementById('bulkUploadModal').classList.remove('hidden');
    }

    function hideBulkUploadModal() {
        document.getElementById('bulkUploadModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('bulkUploadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideBulkUploadModal();
        }
    });
</script>
@endsection