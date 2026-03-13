@extends('layouts.student')

@section('title', 'Take Exam')
@section('heading', 'Take Exam')
@section('subheading', $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto select-none" style="-webkit-user-select: none; user-select: none;">
    <!-- Sticky Timer Bar -->
    <div id="timerBar" class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm mb-6 rounded-t-lg overflow-hidden">
        <div class="px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div id="timerDisplay" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-100 text-blue-800 font-mono text-lg font-semibold">
                        <i class="fas fa-clock"></i>
                        <span id="timer">--:--</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span id="answeredCount">0</span> / {{ $questions->count() }} answered
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="paletteToggle" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-th-large mr-2"></i>
                        Question Palette
                    </button>
                    <button type="button" id="reviewBtn" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Review & Submit
                    </button>
                </div>
            </div>
            <!-- Timer Progress Bar -->
            <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div id="timerProgress" class="h-full bg-blue-500 rounded-full transition-all duration-1000" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Question Palette (collapsible) -->
        <div id="questionPalette" class="border-b border-gray-200 bg-gray-50 p-4 hidden">
            <p class="text-sm font-medium text-gray-700 mb-3">Jump to question (green = answered, blue = current)</p>
            <div id="paletteGrid" class="flex flex-wrap gap-2">
                @foreach($questions as $index => $question)
                <button type="button" 
                        class="palette-btn w-10 h-10 rounded-lg font-medium text-sm transition-all border-2 border-gray-300 bg-white text-gray-700 hover:border-primary-500 hover:bg-primary-50"
                        data-question="{{ $index }}"
                        title="Question {{ $index + 1 }}">
                    {{ $index + 1 }}
                </button>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('student.assessments.submit', $assessment) }}" id="assessmentForm">
            @csrf
            <input type="hidden" name="time_taken_seconds" id="timeTakenSeconds" value="">
            
            <div class="p-6">
                <!-- Question Cards (one visible at a time) -->
                @foreach($questions as $index => $question)
                <div class="question-card border border-gray-200 rounded-lg p-6 {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                        <span class="text-xs text-gray-400">Marks: 4</span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $question->question_text }}</h3>
                    </div>
                    
                    <div class="space-y-3">
                        @php
                            $options = [
                                'A' => $question->option_a,
                                'B' => $question->option_b,
                                'C' => $question->option_c,
                                'D' => $question->option_d,
                            ];
                        @endphp
                        @foreach($options as $label => $option)
                            @if($option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $label }}"
                                           class="answer-input h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           data-question="{{ $index }}">
                                    <span class="ml-3 text-gray-900">{{ $label }}) {{ $option }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="button" id="prevBtn" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled>
                    <i class="fas fa-arrow-left mr-2"></i>
                    Previous
                </button>
                <button type="button" id="nextBtn" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Next
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" id="reviewModalBackdrop"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Review Your Answers</h2>
                <p class="text-sm text-gray-600 mt-1">Verify your answers before submitting. Click a question to go back and edit.</p>
            </div>
            <div class="p-6 overflow-y-auto max-h-96">
                <div id="reviewList" class="space-y-3">
                    @foreach($questions as $index => $question)
                    <div class="review-item flex items-center justify-between p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors" data-question="{{ $index }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium bg-gray-200 text-gray-700">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ Str::limit($question->question_text, 50) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5"><span class="answer-status">Not answered</span></p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-between items-center">
                <button type="button" id="reviewCloseBtn" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Back to Exam
                </button>
                <button type="button" id="confirmSubmitBtn" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Confirm & Submit
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    // Config
    const totalQuestions = {{ $questions->count() }};
    const assessmentStartTime = new Date('{{ (session("assessment_start_time_" . $assessment->id) ?? now())->format("c") }}');
    const timeLimitMinutes = {{ $assessment->time_limit_minutes }};
    const totalTimeSeconds = timeLimitMinutes * 60;

    // Anti-cheat
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());

    // State
    let currentQuestion = 0;
    let timeLeft = Math.max(0, totalTimeSeconds - Math.floor((new Date() - assessmentStartTime) / 1000));
    let timerInterval = null;

    // Elements
    const questionCards = document.querySelectorAll('.question-card');
    const paletteBtns = document.querySelectorAll('.palette-btn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paletteToggle = document.getElementById('paletteToggle');
    const questionPalette = document.getElementById('questionPalette');
    const reviewBtn = document.getElementById('reviewBtn');
    const reviewModal = document.getElementById('reviewModal');
    const reviewCloseBtn = document.getElementById('reviewCloseBtn');
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
    const reviewModalBackdrop = document.getElementById('reviewModalBackdrop');
    const assessmentForm = document.getElementById('assessmentForm');
    const timerDisplay = document.getElementById('timerDisplay');
    const timerProgress = document.getElementById('timerProgress');

    function showQuestion(index) {
        questionCards.forEach((card, i) => {
            card.classList.toggle('hidden', i !== index);
        });
        paletteBtns.forEach((btn, i) => {
            btn.classList.remove('border-primary-600', 'bg-primary-100', 'text-primary-800', 'ring-2', 'ring-primary-300');
            if (i === index) {
                btn.classList.add('border-primary-600', 'bg-primary-100', 'text-primary-800', 'ring-2', 'ring-primary-300');
            }
        });
        currentQuestion = index;
        prevBtn.disabled = index === 0;
        nextBtn.textContent = index === totalQuestions - 1 ? 'Review' : 'Next';
        nextBtn.innerHTML = index === totalQuestions - 1 
            ? '<i class="fas fa-clipboard-check mr-2"></i>Review' 
            : 'Next <i class="fas fa-arrow-right ml-2"></i>';
        updateAnsweredCount();
    }

    function updateAnsweredCount() {
        const answered = document.querySelectorAll('.answer-input:checked').length;
        document.getElementById('answeredCount').textContent = answered;
        paletteBtns.forEach((btn, i) => {
            const hasAns = document.querySelector(`.answer-input[data-question="${i}"]:checked`);
            btn.classList.remove('border-green-500', 'bg-green-100', 'text-green-800', 'border-primary-600', 'bg-primary-100', 'text-primary-800', 'ring-2', 'ring-primary-300');
            btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
            if (hasAns) {
                btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
                btn.classList.add('border-green-500', 'bg-green-100', 'text-green-800');
            }
            if (i === currentQuestion) {
                btn.classList.remove('border-gray-300', 'border-green-500');
                btn.classList.add('border-primary-600', 'bg-primary-100', 'text-primary-800', 'ring-2', 'ring-primary-300');
            }
        });
        document.querySelectorAll('.review-item').forEach((item, i) => {
            const radios = document.querySelectorAll(`.answer-input[data-question="${i}"]`);
            const checked = Array.from(radios).find(r => r.checked);
            const statusEl = item.querySelector('.answer-status');
            if (checked) {
                statusEl.textContent = 'Answered: ' + checked.value;
                statusEl.classList.remove('text-red-600');
                statusEl.classList.add('text-green-600');
            } else {
                statusEl.textContent = 'Not answered';
                statusEl.classList.remove('text-green-600');
                statusEl.classList.add('text-red-600');
            }
        });
    }

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const progress = (timeLeft / totalTimeSeconds) * 100;
        timerProgress.style.width = progress + '%';
        
        if (timeLeft <= 300) {
            timerDisplay.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-800 font-mono text-lg font-semibold animate-pulse';
        } else if (timeLeft <= 600) {
            timerDisplay.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-100 text-amber-800 font-mono text-lg font-semibold';
        }
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            const elapsed = Math.max(1, Math.floor((new Date() - assessmentStartTime) / 1000));
            document.getElementById('timeTakenSeconds').value = elapsed;
            assessmentForm.submit();
        }
        timeLeft--;
    }

    // Event listeners
    prevBtn.addEventListener('click', () => {
        if (currentQuestion > 0) showQuestion(currentQuestion - 1);
    });

    nextBtn.addEventListener('click', () => {
        if (currentQuestion < totalQuestions - 1) {
            showQuestion(currentQuestion + 1);
        } else {
            questionPalette.classList.add('hidden');
            reviewModal.classList.remove('hidden');
            updateAnsweredCount();
        }
    });

    paletteToggle.addEventListener('click', () => {
        questionPalette.classList.toggle('hidden');
    });

    paletteBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            showQuestion(parseInt(btn.dataset.question));
            questionPalette.classList.add('hidden');
        });
    });

    document.querySelectorAll('.answer-input').forEach(input => {
        input.addEventListener('change', updateAnsweredCount);
    });

    reviewBtn.addEventListener('click', () => {
        reviewModal.classList.remove('hidden');
        updateAnsweredCount();
    });

    reviewCloseBtn.addEventListener('click', () => {
        reviewModal.classList.add('hidden');
    });

    reviewModalBackdrop.addEventListener('click', () => {
        reviewModal.classList.add('hidden');
    });

    document.querySelectorAll('.review-item').forEach(item => {
        item.addEventListener('click', () => {
            reviewModal.classList.add('hidden');
            showQuestion(parseInt(item.dataset.question));
        });
    });

    confirmSubmitBtn.addEventListener('click', () => {
        const answered = document.querySelectorAll('.answer-input:checked').length;
        if (answered < totalQuestions) {
            if (confirm(`You have answered ${answered} out of ${totalQuestions} questions. Submit anyway?`)) {
                submitForm();
            }
        } else {
            submitForm();
        }
    });

    function submitForm() {
        const elapsed = Math.max(1, Math.floor((new Date() - assessmentStartTime) / 1000));
        document.getElementById('timeTakenSeconds').value = elapsed;
        assessmentForm.submit();
    }

    assessmentForm.addEventListener('submit', function(e) {
        if (e.submitter && e.submitter.id === 'confirmSubmitBtn') return;
        const answered = document.querySelectorAll('.answer-input:checked').length;
        const elapsed = Math.max(1, Math.floor((new Date() - assessmentStartTime) / 1000));
        document.getElementById('timeTakenSeconds').value = elapsed;
        if (answered < totalQuestions && !confirm(`You have answered ${answered} out of ${totalQuestions} questions. Submit anyway?`)) {
            e.preventDefault();
        }
    });

    // Init
    showQuestion(0);
    updateAnsweredCount();
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);

    // beforeunload warning
    window.addEventListener('beforeunload', (e) => {
        if (timeLeft > 0) e.preventDefault();
    });
})();
</script>
@endsection
