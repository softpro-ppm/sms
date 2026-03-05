@extends('layouts.student')

@section('title', 'Take Exam')
@section('heading', 'Take Exam')
@section('subheading', $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto select-none" style="-webkit-user-select: none; user-select: none;">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $assessment->title }}</h2>
            <p class="text-gray-600 mb-4">{{ $assessment->description }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Time Limit</p>
                            <p class="font-semibold text-gray-900">{{ $assessment->time_limit_minutes }} minutes</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-question-circle text-green-600 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Total Questions</p>
                            <p class="font-semibold text-gray-900">{{ $questions->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-percentage text-yellow-600 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600">Passing Percentage</p>
                            <p class="font-semibold text-gray-900">{{ $assessment->passing_percentage }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('student.assessments.submit', $assessment) }}" id="assessmentForm">
            @csrf
            <input type="hidden" name="time_taken_seconds" id="timeTakenSeconds" value="">
            
            <div class="space-y-8">
                @foreach($questions as $index => $question)
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                Question {{ $index + 1 }}: {{ $question->question_text }}
                            </h3>
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
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $label }}"
                                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                        <span class="ml-3 text-gray-900">{{ $label }}) {{ $option }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <p>Please answer all questions before submitting.</p>
                </div>
                
                <button type="submit" 
                        class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    Submit Exam
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Store exact start time for accurate elapsed calculation (persists across tab switches)
const assessmentStartTime = new Date('{{ (session("assessment_start_time_" . $assessment->id) ?? now())->format("c") }}');

// Disable right-click and text selection/copy
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
const timeLimitMinutes = {{ $assessment->time_limit_minutes }};
const totalTimeSeconds = timeLimitMinutes * 60;

// Calculate elapsed time
const now = new Date();
const elapsedSeconds = Math.floor((now - assessmentStartTime) / 1000);
let timeLeft = Math.max(0, totalTimeSeconds - elapsedSeconds);

const timerElement = document.createElement('div');
timerElement.className = 'fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
timerElement.innerHTML = `
    <div class="flex items-center">
        <i class="fas fa-clock mr-2"></i>
        <span id="timer">${Math.floor(timeLeft / 60)}:${(timeLeft % 60).toString().padStart(2, '0')}</span>
    </div>
`;
document.body.appendChild(timerElement);

const timer = setInterval(() => {
    timeLeft--;
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color when time is running low
    if (timeLeft <= 300) { // 5 minutes
        timerElement.className = 'fixed top-4 right-4 bg-red-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-pulse';
    }
    
    if (timeLeft <= 0) {
        clearInterval(timer);
        const elapsed = Math.max(1, Math.floor((new Date() - assessmentStartTime) / 1000));
        document.getElementById('timeTakenSeconds').value = elapsed;
        alert('Time is up! Your assessment will be submitted automatically.');
        document.getElementById('assessmentForm').submit();
    }
}, 1000);

// Prevent form submission if not all questions are answered + send elapsed time
document.getElementById('assessmentForm').addEventListener('submit', function(e) {
    const totalQuestions = {{ $questions->count() }};
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    // Calculate actual elapsed time (includes time away from tab)
    const elapsedSeconds = Math.floor((new Date() - assessmentStartTime) / 1000);
    document.getElementById('timeTakenSeconds').value = Math.max(1, elapsedSeconds);
    
    if (answeredQuestions < totalQuestions) {
        e.preventDefault();
        if (confirm(`You have answered ${answeredQuestions} out of ${totalQuestions} questions. Are you sure you want to submit?`)) {
            document.getElementById('timeTakenSeconds').value = Math.max(1, Math.floor((new Date() - assessmentStartTime) / 1000));
            this.submit();
        }
    }
});
</script>
@endsection
