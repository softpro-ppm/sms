@extends('layouts.student')

@section('title', 'Exam Instructions')
@section('heading', 'Exam Instructions')
@section('subheading', $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <!-- Exam Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $assessment->title }}</h1>
            <p class="text-lg text-gray-600">{{ $assessment->description }}</p>
        </div>

        <!-- Exam Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Time Limit</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $assessment->time_limit_minutes }} minutes</p>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-question-circle text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Total Questions</h3>
                <p class="text-2xl font-bold text-green-600">{{ $assessment->total_questions ?? 25 }}</p>
            </div>
            
            <div class="bg-yellow-50 p-6 rounded-lg text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Passing Score</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $assessment->passing_percentage }}%</p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Important Instructions
            </h2>
            
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-blue-600 text-sm font-bold">1</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Time Management</h3>
                        <p class="text-gray-600">You have exactly <strong>{{ $assessment->time_limit_minutes }} minutes</strong> to complete this assessment. The timer will start as soon as you click "Start Exam".</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-blue-600 text-sm font-bold">2</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Question Format</h3>
                        <p class="text-gray-600">This assessment contains <strong>{{ $assessment->total_questions ?? 25 }} multiple-choice questions</strong>. Each question has 4 options (A, B, C, D). Each correct answer carries <strong>{{ $assessment->total_questions ? round(100 / $assessment->total_questions, 1) : 4 }} marks</strong> (Total 100).</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-blue-600 text-sm font-bold">3</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Answering Questions</h3>
                        <p class="text-gray-600">Select the most appropriate answer for each question. You can change your answers before submitting.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-blue-600 text-sm font-bold">4</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Submission</h3>
                        <p class="text-gray-600">Click "Submit Exam" when you have completed all questions. The assessment will be automatically submitted when time expires.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-blue-600 text-sm font-bold">5</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Technical Requirements</h3>
                        <p class="text-gray-600">Ensure you have a stable internet connection. Do not refresh the page or navigate away during the assessment.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rules and Regulations -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-red-900 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                Rules & Regulations
            </h2>
            
            <ul class="space-y-2 text-red-800">
                <li class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    <span>Do not use any external resources, books, or notes</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    <span>Do not communicate with others during the assessment</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    <span>Do not take screenshots or record the assessment</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    <span>Ensure you are in a quiet environment without distractions</span>
                </li>
            </ul>
        </div>

        <!-- Start Exam Button -->
        <div class="text-center">
            <div class="mb-4">
                <label class="flex items-center justify-center text-gray-700">
                    <input type="checkbox" id="agreeTerms" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded mr-2">
                    <span class="text-sm">I have read and understood all the instructions and agree to follow the rules</span>
                </label>
            </div>
            
            <button id="startExamBtn" 
                    disabled
                    class="bg-gray-400 text-white px-8 py-4 rounded-lg text-lg font-semibold cursor-not-allowed transition-all duration-300"
                    onclick="startExam()">
                <i class="fas fa-play mr-2"></i>
                Start Exam
            </button>
            
            <p class="text-sm text-gray-500 mt-3">
                Once you start, the timer will begin and cannot be paused
            </p>
        </div>
    </div>
</div>

<script>
// Enable/disable start button based on checkbox
document.getElementById('agreeTerms').addEventListener('change', function() {
    const startBtn = document.getElementById('startExamBtn');
    if (this.checked) {
        startBtn.disabled = false;
        startBtn.className = 'bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl';
    } else {
        startBtn.disabled = true;
        startBtn.className = 'bg-gray-400 text-white px-8 py-4 rounded-lg text-lg font-semibold cursor-not-allowed transition-all duration-300';
    }
});

function startExam() {
    if (document.getElementById('agreeTerms').checked) {
        // Show confirmation dialog
        if (confirm('Are you ready to start the assessment? Once you begin, the timer will start and cannot be paused.')) {
            window.location.href = "{{ route('student.assessments.start', $assessment) }}";
        }
    }
}

// No need to prevent page refresh on instructions page
// The beforeunload warning is only needed during the actual assessment
</script>
@endsection
