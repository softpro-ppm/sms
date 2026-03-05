<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $query = QuestionBank::with('course');

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by subject
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Filter by difficulty
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('option_a', 'like', "%{$search}%")
                  ->orWhere('option_b', 'like', "%{$search}%")
                  ->orWhere('option_c', 'like', "%{$search}%")
                  ->orWhere('option_d', 'like', "%{$search}%");
            });
        }

        $questions = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());
        $courses = Course::where('is_active', true)->get();
        
        // Get unique subjects for filter dropdown
        $subjects = QuestionBank::select('subject')->distinct()->orderBy('subject')->pluck('subject');

        return view('admin.question-banks.index', compact('questions', 'courses', 'subjects'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.question-banks.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'subject' => 'required|string|max:100',
            'question_text' => 'required|string|max:1000',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_answer' => 'required|in:A,B,C,D',
            'difficulty_level' => 'required|in:easy,medium,hard'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        QuestionBank::create($request->all());

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question added successfully!');
    }

    public function show(QuestionBank $questionBank)
    {
        $questionBank->load('course');
        return view('admin.question-banks.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.question-banks.edit', compact('questionBank', 'courses'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'subject' => 'required|string|max:100',
            'question_text' => 'required|string|max:1000',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_answer' => 'required|in:A,B,C,D',
            'difficulty_level' => 'required|in:easy,medium,hard'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $questionBank->update($request->all());

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Question deleted successfully!');
    }

    public function toggleStatus(QuestionBank $questionBank)
    {
        $questionBank->update(['is_active' => !$questionBank->is_active]);

        $status = $questionBank->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Question {$status} successfully!");
    }

    public function bulkUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('csv_file');
        $path = $file->store('temp');
        $fullPath = storage_path('app/' . $path);

        try {
            $handle = fopen($fullPath, 'r');
            $header = fgetcsv($handle); // Skip header row
            
            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 7) {
                    $errors[] = "Row " . ($imported + 2) . ": Insufficient columns";
                    continue;
                }

                $data = [
                    'course_id' => $request->course_id,
                    'subject' => trim($row[0]),
                    'question_text' => trim($row[1]),
                    'option_a' => trim($row[2]),
                    'option_b' => trim($row[3]),
                    'option_c' => trim($row[4]),
                    'option_d' => trim($row[5]),
                    'correct_answer' => strtoupper(trim($row[6])),
                    'difficulty_level' => 'easy' // Default to easy for bulk upload
                ];

                // Validate row data
                $rowValidator = Validator::make($data, [
                    'subject' => 'required|string|max:100',
                    'question_text' => 'required|string|max:1000',
                    'option_a' => 'required|string|max:500',
                    'option_b' => 'required|string|max:500',
                    'option_c' => 'required|string|max:500',
                    'option_d' => 'required|string|max:500',
                    'correct_answer' => 'required|in:A,B,C,D'
                ]);

                if ($rowValidator->fails()) {
                    $errors[] = "Row " . ($imported + 2) . ": " . implode(', ', $rowValidator->errors()->all());
                    continue;
                }

                QuestionBank::create($data);
                $imported++;
            }

            fclose($handle);
            Storage::delete($path);

            $message = "Successfully imported {$imported} questions!";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', $errors);
            }

            return redirect()->route('admin.question-banks.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Storage::delete($path);
            return redirect()->back()
                ->with('error', 'Error processing CSV file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $template = "Subject,Question,Option_A,Option_B,Option_C,Option_D,Correct_Answer\n";
        $template .= "MS Office Fundamentals,What is Microsoft Office?,A software suite,A single program,A hardware device,An operating system,A\n";
        $template .= "MS Word,What is the default file extension for Word documents?,.docx,.txt,.pdf,.xlsx,A\n";
        $template .= "MS Excel,What is a cell in Excel?,Intersection of row and column,A single row,A single column,A worksheet,A\n";
        $template .= "MS PowerPoint,How do you add a new slide?,Ctrl+M,Ctrl+N,Ctrl+O,Ctrl+P,A\n";
        $template .= "MS Paint,What is MS Paint used for?,Image editing,Word processing,Spreadsheet creation,Email management,A\n";

        return response($template)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="question_bank_template.csv"');
    }

    public function export(Request $request)
    {
        $query = QuestionBank::query();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('option_a', 'like', "%{$search}%")
                  ->orWhere('option_b', 'like', "%{$search}%")
                  ->orWhere('option_c', 'like', "%{$search}%")
                  ->orWhere('option_d', 'like', "%{$search}%");
            });
        }

        $questions = $query->orderBy('created_at', 'desc')->get();

        return response()->streamDownload(function () use ($questions) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Subject', 'Question', 'Option_A', 'Option_B', 'Option_C', 'Option_D', 'Correct_Answer']);
            foreach ($questions as $question) {
                fputcsv($output, [
                    $question->subject,
                    $question->question_text,
                    $question->option_a,
                    $question->option_b,
                    $question->option_c,
                    $question->option_d,
                    strtoupper($question->correct_answer),
                ]);
            }
            fclose($output);
        }, 'question_bank_export.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function getSubjectsByCourse(Request $request)
    {
        $courseId = $request->course_id;
        $subjects = QuestionBank::where('course_id', $courseId)
            ->select('subject')
            ->distinct()
            ->orderBy('subject')
            ->pluck('subject');

        return response()->json($subjects);
    }

    public function getQuestionStats(Request $request)
    {
        $courseId = $request->course_id;
        
        $stats = QuestionBank::where('course_id', $courseId)
            ->select('subject', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'))
            ->groupBy('subject')
            ->orderBy('subject')
            ->get();

        return response()->json($stats);
    }
}
