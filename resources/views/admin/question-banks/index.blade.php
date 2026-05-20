@extends('layouts.admin')

@section('title', 'Question Banks')
@section('page-title', 'Question Banks')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-layer-group text-[10px]"></i>
                    Question Bank Queue
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Manage question coverage, subject mix, and difficulty balance.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review active questions, filter by course and subject, and keep the bank ready for new exams and uploads.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="showBulkUploadModal()"
                        class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                    <i class="fas fa-upload text-xs"></i>
                    Bulk upload
                </button>
                <a href="{{ route('admin.question-banks.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-file-export text-xs"></i>
                    Export CSV
                </a>
                <a href="{{ route('admin.question-banks.create') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-plus text-xs"></i>
                    Add question
                </a>
            </div>
        </div>
        <div class="grid gap-4 border-t border-slate-200 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total questions</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $statsTotal ?? $questions->total() }}</p>
                <p class="mt-2 text-sm text-slate-600">Questions currently available.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Active questions</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $statsActive ?? 0 }}</p>
                <p class="mt-2 text-sm text-slate-600">Live and available for exams.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Courses</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $courses->count() }}</p>
                <p class="mt-2 text-sm text-slate-600">Course banks in this workspace.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Subjects</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $subjects->count() }}</p>
                <p class="mt-2 text-sm text-slate-600">Subjects represented in the bank.</p>
            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="grid gap-4 px-6 py-5 xl:grid-cols-[260px,minmax(0,1fr)] xl:items-end">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Filters</div>
                <h3 class="mt-3 text-xl font-semibold tracking-tight text-slate-900">Course, subject, and difficulty</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Narrow the bank by coverage area or search for a specific question quickly.</p>
            </div>
            <form method="GET" action="{{ route('admin.question-banks.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <input type="text" name="search" id="search"
                       data-live-search
                       class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100 xl:col-span-2"
                       placeholder="Search questions..."
                       value="{{ request('search') }}">
                <select name="course_id" id="course_id" data-live-filter class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                <select name="subject" id="subject" data-live-filter class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">All subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>
                            {{ $subject }}
                        </option>
                    @endforeach
                </select>
                <select name="difficulty_level" id="difficulty_level" data-live-filter class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">All levels</option>
                    <option value="easy" {{ request('difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ request('difficulty_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ request('difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
                <div class="flex flex-wrap items-center gap-3 md:col-span-2 xl:col-span-5">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-slate-500">Rows</label>
                        <select id="per_page" name="per_page" data-live-rows class="rounded-2xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            @foreach([10,20,50,100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('admin.question-banks.index') }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                        <i class="fas fa-times text-xs"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Question records</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Review and act on the current question bank</h3>
            </div>
            <div class="text-sm text-slate-500">{{ $questions->total() }} total records</div>
        </div>
        <div class="overflow-hidden">
            @if($questions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">S.No</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Course</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Subject</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Question</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Difficulty</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Created</th>
                                <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($questions as $question)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-900">
                                        {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-900">
                                        {{ $question->course->name }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            {{ $question->subject }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-900 max-w-xs">
                                        <div class="max-w-[280px] truncate">
                                            {{ Str::limit($question->question_text, 100) }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                            {{ $question->difficulty_level == 'easy' ? 'bg-emerald-50 text-emerald-700' : 
                                               ($question->difficulty_level == 'medium' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                            {{ ucfirst($question->difficulty_level) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                            {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-500">
                                        {{ $question->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.question-banks.show', $question) }}" 
                                               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600 transition hover:border-blue-300 hover:bg-blue-100"
                                               title="View question">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.question-banks.edit', $question) }}" 
                                               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-600 transition hover:border-indigo-300 hover:bg-indigo-100"
                                               title="Edit question">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.question-banks.toggle-status', $question) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border {{ $question->is_active ? 'border-amber-200 bg-amber-50 text-amber-600 hover:border-amber-300 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:border-emerald-300 hover:bg-emerald-100' }} transition"
                                                        title="{{ $question->is_active ? 'Deactivate question' : 'Activate question' }}">
                                                    <i class="fas fa-{{ $question->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.question-banks.destroy', $question) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to delete this question?')"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition hover:border-rose-300 hover:bg-rose-100"
                                                        title="Delete question">
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
                <div class="px-5 py-4 border-t border-gray-200">
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
                           class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
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
