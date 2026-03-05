<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\AssessmentResult;
use App\Mail\CertificateIssuedMail;
use App\Services\CertificateTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CertificatesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $query = Certificate::with(['student', 'course', 'batch', 'assessmentResult']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by batch
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'issued') {
                $query->where('is_issued', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_issued', false);
            }
        }

        // Filter by student
        if ($request->filled('student_search')) {
            $search = $request->student_search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $certificates = $query->orderBy('issue_date', 'desc')
            ->paginate($perPage)
            ->appends($request->query());
        
        // Get filter options
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('batch_name')->get();

        // Get statistics
        $stats = [
            'total_certificates' => Certificate::count(),
            'issued_certificates' => Certificate::where('is_issued', true)->count(),
            'pending_certificates' => Certificate::where('is_issued', false)->count(),
            'total_students' => Certificate::distinct('student_id')->count(),
            'this_month' => Certificate::whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'courses', 'batches', 'stats'));
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['student', 'course', 'batch', 'assessmentResult']);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function preview(Certificate $certificate)
    {
        $certificate->load(['student', 'course', 'batch', 'assessmentResult']);

        $templateService = app(CertificateTemplateService::class);
        $html = $templateService->generateHtml($certificate);

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    public function generate(Certificate $certificate)
    {
        try {
            // Generate certificate number and update certificate
            $certificateNumber = $this->generateCertificateNumber();
            $certificate->update([
                'certificate_number' => $certificateNumber,
                'is_issued' => true,
                'issue_date' => now(),
            ]);

            // Generate certificate HTML file
            $this->generateCertificateFile($certificate);

            // Send certificate issued email
            try {
                $certificate->load(['course', 'student']);
                Mail::to($certificate->student->email)->send(new CertificateIssuedMail($certificate));
                try {
                    app(\App\Services\WhatsAppNotificationService::class)->sendCertificateIssued($certificate);
                } catch (\Exception $e) {
                    \Log::error('Certificate WhatsApp failed: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                \Log::error('Certificate email failed: ' . $e->getMessage());
            }

            return redirect()->route('admin.certificates.show', $certificate)
                ->with('success', 'Certificate generated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    public function download(Certificate $certificate)
    {
        // Generate certificate file if it doesn't exist or uses old "Certificate of Completion" format
        $needsRegeneration = !$certificate->certificate_file_path || !Storage::exists($certificate->certificate_file_path);
        if (!$needsRegeneration) {
            $existingContent = Storage::get($certificate->certificate_file_path);
            $needsRegeneration = $existingContent && str_contains($existingContent, 'CERTIFICATE OF COMPLETION');
        }
        if ($needsRegeneration) {
            $this->generateCertificateFile($certificate);
        }

        if (!Storage::exists($certificate->certificate_file_path)) {
            return redirect()->back()
                ->with('error', 'Certificate file could not be generated.');
        }

        return Storage::download($certificate->certificate_file_path, 
            'certificate_' . $certificate->certificate_number . '.html');
    }

    public function revoke(Certificate $certificate)
    {
        $certificate->update(['is_issued' => false]);
        
        return redirect()->back()
            ->with('success', 'Certificate revoked successfully!');
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'approved')->orderBy('full_name')->get();
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('batch_name')->get();
        $assessmentResults = AssessmentResult::where('is_passed', true)
            ->with(['student', 'assessment'])
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('admin.certificates.create', compact('students', 'courses', 'batches', 'assessmentResults'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'assessment_result_id' => 'nullable|exists:assessment_results,id',
            'certificate_content' => 'nullable|string',
        ]);

        // Check if certificate already exists for this student and course
        $existingCertificate = Certificate::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existingCertificate) {
            return redirect()->back()
                ->with('error', 'Certificate already exists for this student and course.')
                ->withInput();
        }

        $certificate = Certificate::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'assessment_result_id' => $request->assessment_result_id,
            'certificate_content' => $request->certificate_content,
            'is_issued' => false,
        ]);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate created successfully!');
    }

    private function generateCertificateNumber(): string
    {
        $prefix = 'CERT';
        $year = now()->year;
        $month = now()->format('m');
        
        // Get the last certificate number for this month
        $lastCertificate = Certificate::where('certificate_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('certificate_number', 'desc')
            ->first();
        
        if ($lastCertificate) {
            $lastNumber = intval(substr($lastCertificate->certificate_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getBatchesByCourse(Request $request)
    {
        $courseId = $request->course_id;
        $batches = Batch::where('course_id', $courseId)
            ->where('is_active', true)
            ->where(function ($query) {
                $today = Carbon::today();
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->orderBy('batch_name')
            ->get();

        return response()->json($batches);
    }

    public function getStats(Request $request)
    {
        $query = Certificate::query();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $stats = [
            'total_certificates' => $query->count(),
            'issued_certificates' => $query->clone()->where('is_issued', true)->count(),
            'pending_certificates' => $query->clone()->where('is_issued', false)->count(),
        ];

        return response()->json($stats);
    }

    private function generateCertificateFile(Certificate $certificate)
    {
        $templateService = app(CertificateTemplateService::class);

        // Generate HTML content using Training Certification template
        $htmlContent = $templateService->generateHtml($certificate);
        
        // Create file path
        $fileName = 'certificate_' . $certificate->certificate_number . '.html';
        $filePath = 'certificates/' . $fileName;
        
        // Store the file
        Storage::put($filePath, $htmlContent);
        
        // Update certificate with file path
        $certificate->update(['certificate_file_path' => $filePath]);
        
        return $filePath;
    }

}
