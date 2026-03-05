<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\StudentAssessmentAttempt;
use App\Models\StudentAttemptQuestion;
use App\Models\QuestionBank;
use App\Models\AssessmentResult;
use Illuminate\Support\Facades\DB;

class AssessmentGenerationService
{
    public function generateAssessmentForStudent($studentId, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        
        // Check if assessment can generate questions
        if (!$assessment->canGenerateAssessment()) {
            throw new \Exception('Assessment cannot generate questions. Insufficient questions in question bank.');
        }

        // Get the next attempt number for this student
        $lastAttempt = StudentAssessmentAttempt::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('attempt_number', 'desc')
            ->first();

        $attemptNumber = $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;

        // Create new attempt
        $attempt = StudentAssessmentAttempt::create([
            'student_id' => $studentId,
            'assessment_id' => $assessmentId,
            'attempt_number' => $attemptNumber,
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        // Generate random questions (5 from each of 5 subjects = 25)
        $questions = $this->generateRandomQuestions($assessment);
        
        // Create attempt questions
        foreach ($questions as $question) {
            StudentAttemptQuestion::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'student_answer' => null,
                'is_correct' => false,
                'marks_obtained' => 0
            ]);
        }

        return $attempt;
    }

    public function generateRandomQuestions(Assessment $assessment)
    {
        $questions = collect();
        $subjects = $assessment->available_subjects;
        
        // Take exactly 5 questions from each of the first 5 subjects
        $selectedSubjects = array_slice($subjects, 0, 5);
        
        foreach ($selectedSubjects as $subject) {
            $subjectQuestions = QuestionBank::where('course_id', $assessment->course_id)
                ->where('subject', $subject)
                ->where('is_active', true)
                ->inRandomOrder()
                ->take(5)
                ->get();

            if ($subjectQuestions->count() < 5) {
                throw new \Exception("Insufficient questions for subject: {$subject}. Need at least 5 questions.");
            }
            
            $questions = $questions->merge($subjectQuestions);
        }

        return $questions->shuffle();
    }

    public function submitAssessment($attemptId, $answers)
    {
        $attempt = StudentAssessmentAttempt::with(['assessment', 'student'])->findOrFail($attemptId);
        
        if ($attempt->status !== 'in_progress') {
            throw new \Exception('Assessment is not in progress.');
        }

        DB::beginTransaction();
        
        try {
            // Update attempt questions with answers
            $correctAnswers = 0;
            $totalMarks = 0;
            $subjectWiseMarks = [];

            foreach ($answers as $questionId => $answer) {
                $attemptQuestion = StudentAttemptQuestion::where('attempt_id', $attemptId)
                    ->where('question_id', $questionId)
                    ->first();

                if ($attemptQuestion) {
                    $question = $attemptQuestion->question;
                    $isCorrect = $question->isCorrectAnswer($answer);
                    $marks = $isCorrect ? 4 : 0;

                    $attemptQuestion->update([
                        'student_answer' => $answer,
                        'is_correct' => $isCorrect,
                        'marks_obtained' => $marks
                    ]);

                    if ($isCorrect) {
                        $correctAnswers++;
                        $totalMarks += 4;
                    }

                    // Track subject-wise marks
                    $subject = $question->subject;
                    if (!isset($subjectWiseMarks[$subject])) {
                        $subjectWiseMarks[$subject] = ['total' => 0, 'correct' => 0, 'marks' => 0];
                    }
                    $subjectWiseMarks[$subject]['total']++;
                    if ($isCorrect) {
                        $subjectWiseMarks[$subject]['correct']++;
                        $subjectWiseMarks[$subject]['marks'] += 4;
                    }
                }
            }

            // Update attempt status
            $attempt->update([
                'completed_at' => now(),
                'status' => 'completed'
            ]);

            // Determine grade
            $grade = $this->calculateGrade($totalMarks);
            $isPassed = $totalMarks >= 35;

            // Create assessment result
            $result = AssessmentResult::create([
                'student_id' => $attempt->student_id,
                'assessment_id' => $attempt->assessment_id,
                'attempt_id' => $attempt->id,
                'enrollment_id' => $attempt->student->enrollments()->where('course_id', $attempt->assessment->course_id)->first()?->id,
                'attempt_number' => $attempt->attempt_number,
                'total_questions' => 25,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => 25 - $correctAnswers,
                'total_marks' => $totalMarks,
                'percentage' => ($totalMarks / 100) * 100,
                'grade' => $grade,
                'is_passed' => $isPassed,
                'subject_wise_marks' => $subjectWiseMarks,
                'started_at' => $attempt->started_at,
                'completed_at' => $attempt->completed_at,
                'time_taken_minutes' => $attempt->duration
            ]);

            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calculateGrade($totalMarks)
    {
        if ($totalMarks >= 80) return 'A+';
        if ($totalMarks >= 60) return 'A';
        if ($totalMarks >= 40) return 'B';
        return 'Fail';
    }

    public function canStudentRetake($studentId, $assessmentId)
    {
        $lastResult = AssessmentResult::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('attempt_number', 'desc')
            ->first();

        return !$lastResult || !$lastResult->isPassed();
    }

    public function getStudentAssessmentHistory($studentId, $assessmentId)
    {
        return AssessmentResult::where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('attempt_number', 'asc')
            ->get();
    }

    public function getAssessmentStatistics($assessmentId)
    {
        $results = AssessmentResult::where('assessment_id', $assessmentId)->get();
        
        $stats = [
            'total_attempts' => $results->count(),
            'passed' => $results->where('is_passed', true)->count(),
            'failed' => $results->where('is_passed', false)->count(),
            'average_marks' => $results->avg('total_marks'),
            'grade_distribution' => [
                'A+' => $results->where('grade', 'A+')->count(),
                'A' => $results->where('grade', 'A')->count(),
                'B' => $results->where('grade', 'B')->count(),
                'Fail' => $results->where('grade', 'Fail')->count()
            ]
        ];

        return $stats;
    }
}
