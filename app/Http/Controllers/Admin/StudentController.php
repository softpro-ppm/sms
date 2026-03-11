<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\CreditAllocation;
use App\Models\StudentDocument;
use App\Mail\AccountApprovedMail;
use App\Mail\EnrollmentConfirmationMail;
use App\Mail\StudentRegistrationMail;
use App\Services\EnrollmentNumberService;
use App\Services\DocumentUploadService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));

        $studentsQuery = Student::with(['user', 'enrollments.batch.course'])
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }]);

        if ($search !== '') {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('aadhar_number', 'like', '%' . $search . '%')
                    ->orWhere('whatsapp_number', 'like', '%' . $search . '%');
            });
        }

        $students = $studentsQuery
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $stats = [
            'total_students' => Student::count(),
            'approved_students' => Student::where('status', 'approved')->count(),
            'pending_students' => Student::where('status', 'pending')->count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function show(Student $student)
    {
        $student->load(['user', 'enrollments.batch.course', 'payments', 'assessmentResults.assessment']);
        
        return view('admin.students.show', compact('student'));
    }

    public function approve(Student $student)
    {
        // Update student status
        $student->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        // Activate user account
        if ($student->user) {
            $student->user->update(['is_active' => true]);
        }

        // Send account approved email
        try {
            Mail::to($student->email)->send(new AccountApprovedMail($student));
        } catch (\Exception $e) {
            \Log::error('Account approved email failed: ' . $e->getMessage());
        }
        try {
            app(WhatsAppNotificationService::class)->sendAccountApproved($student, ['email' => $student->email, 'password' => $student->whatsapp_number]);
        } catch (\Exception $e) {
            \Log::error('Account approved WhatsApp failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Student {$student->full_name} approved successfully!");
    }

    public function reject(Student $student)
    {
        $student->update(['status' => 'rejected']);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$student->full_name} rejected successfully!");
    }

    public function enroll(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'batch_id' => 'required|exists:batches,id',
            'enrollment_date' => 'required|date',
            'total_fee' => 'required|numeric|min:0',
            'credit_to_apply' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $batch = Batch::find($request->batch_id);

        // Check if student is already enrolled in this batch
        $existingEnrollment = Enrollment::where('student_id', $student->id)
            ->where('batch_id', $request->batch_id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'Student is already enrolled in this batch.');
        }

        // Check batch capacity
        if ($batch->max_students && $batch->enrollments()->where('status', 'active')->count() >= $batch->max_students) {
            return redirect()->back()
                ->with('error', 'Batch is full. Cannot enroll more students.');
        }

        // Calculate total fees: Registration (₹100) + Course Fee + Assessment (₹100)
        $registrationFee = 100.00;
        $assessmentFee = 100.00;
        $totalFees = $registrationFee + $request->total_fee + $assessmentFee;
        $creditToApply = min(
            (float) ($request->credit_to_apply ?? 0),
            (float) $student->credit_balance,
            $totalFees
        );

        if ($creditToApply > 0 && $creditToApply > (float) $student->credit_balance) {
            return redirect()->back()
                ->with('error', 'Insufficient credit balance. Available: ₹' . number_format($student->credit_balance, 0));
        }

        // Generate unique enrollment number
        $enrollmentNumber = EnrollmentNumberService::generateEnrollmentNumber();

        // Create enrollment with total fees breakdown
        $enrollment = Enrollment::create([
            'enrollment_number' => $enrollmentNumber,
            'student_id' => $student->id,
            'batch_id' => $request->batch_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => 'active',
            'total_fee' => $totalFees, // Total of all fees
            'paid_amount' => 0,
            'outstanding_amount' => $totalFees,
            'is_eligible_for_assessment' => false,
            // Store fee breakdown
            'registration_fee' => $registrationFee,
            'course_fee' => $request->total_fee,
            'assessment_fee' => $assessmentFee,
        ]);

        // Apply credit if any
        if ($creditToApply > 0) {
            try {
                $creditService = new \App\Services\StudentCreditService();
                $creditService->applyCreditToEnrollment($enrollment, $creditToApply);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Failed to apply credit: ' . $e->getMessage());
            }
        }

        // Send enrollment confirmation email
        try {
            $enrollment->load(['batch.course', 'student']);
            Mail::to($student->email)->send(new EnrollmentConfirmationMail($enrollment));
        } catch (\Exception $e) {
            \Log::error('Enrollment confirmation email failed: ' . $e->getMessage());
        }
        try {
            app(WhatsAppNotificationService::class)->sendEnrollmentConfirmation($enrollment);
        } catch (\Exception $e) {
            \Log::error('Enrollment WhatsApp failed: ' . $e->getMessage());
        }

        $msg = "Student enrolled in {$batch->batch_name} successfully! Enrollment Number: {$enrollmentNumber}. Total fees: ₹{$totalFees}";
        if ($creditToApply > 0) {
            $msg .= " (₹" . number_format($creditToApply, 0) . " credit applied, ₹" . number_format($enrollment->outstanding_amount, 0) . " outstanding)";
        } else {
            $msg .= " (Registration: ₹{$registrationFee} + Course: ₹{$request->total_fee} + Assessment: ₹{$assessmentFee})";
        }
        return redirect()->route('admin.payments.create', ['student_id' => $student->id, 'enrollment_id' => $enrollment->id])
            ->with('success', $msg);
    }

    public function dropEnrollment(Enrollment $enrollment)
    {
        $student = $enrollment->student;
        $batch = $enrollment->batch;
        $paidAmount = (float) $enrollment->paid_amount;

        try {
            if ($paidAmount > 0) {
                $creditService = new \App\Services\StudentCreditService();
                $creditService->addCreditFromEnrollment($enrollment, 'enrollment_drop');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to transfer credit: ' . $e->getMessage());
        }

        $enrollment->update(['status' => 'dropped']);

        $msg = "Student dropped from {$batch->batch_name} successfully!";
        if ($paidAmount > 0) {
            $msg .= " ₹" . number_format($paidAmount, 0) . " transferred to student credit balance.";
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', $msg);
    }

    public function create()
    {
        // Ensure session is started for CSRF token generation
        if (!session()->isStarted()) {
            session()->start();
        }
        
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->with('course')->orderBy('batch_name')->get();
        
        return view('admin.students.create', compact('courses', 'batches'));
    }

    public function store(Request $request)
    {
        // Ensure session is started for CSRF token validation
        if (!session()->isStarted()) {
            session()->start();
        }

        $request->merge([
            'email' => strtolower(trim((string) $request->email)),
        ]);
        
        $validator = Validator::make($request->all(), [
            'aadhar_number' => 'required|string|size:12|regex:/^[0-9]{12}$/|unique:students,aadhar_number',
            'full_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'qualification' => 'required|string|in:ITI,Post Graduate,Below SSC,SSC,Intermediate,Graduation,B Tech,Diploma',
            'email' => 'required|string|email|max:255|unique:students,email|unique:users,email',
            'whatsapp_number' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|size:6|regex:/^[0-9]{6}$/',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'aadhar' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'certificate' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create student record - Admin/Reception registration is always approved
            $student = Student::create([
                'aadhar_number' => $request->aadhar_number,
                'full_name' => $request->full_name,
                'father_name' => $request->father_name,
                'gender' => $request->gender,
                'qualification' => $request->qualification,
                'email' => $request->email,
                'phone' => $request->whatsapp_number, // Using WhatsApp number as phone
                'whatsapp_number' => $request->whatsapp_number,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'status' => 'approved', // Admin/Reception registration is always approved
                'approved_at' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withErrors(['email' => 'Email already exists. Please use a different email.'])
                ->withInput();
        }

        // Create user account - Email as username, WhatsApp number as password
        $user = User::create([
            'name' => $student->full_name,
            'email' => $student->email,
            'password' => Hash::make($student->whatsapp_number), // WhatsApp number as password
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => true, // Always active for admin/reception registration
        ]);

        // Handle document uploads
        $documentService = new DocumentUploadService();
        
        try {
            // Upload photo
            if ($request->hasFile('photo')) {
                $documentService->uploadDocument(
                    $request->file('photo'),
                    $student->id,
                    'photo',
                    $request->input('photo_crop_data') ? json_decode($request->input('photo_crop_data'), true) : null
                );
            }

            // Upload Aadhar
            if ($request->hasFile('aadhar')) {
                $documentService->uploadDocument(
                    $request->file('aadhar'),
                    $student->id,
                    'aadhar'
                );
            }

            // Upload Qualification Certificate
            if ($request->hasFile('certificate')) {
                $documentService->uploadDocument(
                    $request->file('certificate'),
                    $student->id,
                    'qualification_certificate'
                );
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the registration
            \Log::error('Failed to upload documents: ' . $e->getMessage());
        }

        // Send registration confirmation email
        try {
            Mail::to($student->email)->send(new StudentRegistrationMail($student));
        } catch (\Exception $e) {
            // Log the error but don't fail the registration
            \Log::error('Failed to send registration email: ' . $e->getMessage());
        }
        try {
            app(WhatsAppNotificationService::class)->sendRegistration($student, ['email' => $student->email, 'password' => $student->whatsapp_number]);
        } catch (\Exception $e) {
            \Log::error('Registration WhatsApp failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student created successfully! Login credentials: Email: ' . $student->email . ', Password: ' . $student->whatsapp_number);
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'aadhar_number' => 'required|string|size:12|regex:/^[0-9]{12}$/|unique:students,aadhar_number,' . $student->id,
            'full_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'qualification' => 'required|string|in:ITI,Post Graduate,Below SSC,SSC,Intermediate,Graduation,B Tech,Diploma',
            'email' => 'required|string|email|max:255|unique:students,email,' . $student->id . ($student->user ? '|unique:users,email,' . $student->user->id : ''),
            'whatsapp_number' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|size:6|regex:/^[0-9]{6}$/',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update([
            'aadhar_number' => $request->aadhar_number,
            'full_name' => $request->full_name,
            'father_name' => $request->father_name,
            'gender' => $request->gender,
            'qualification' => $request->qualification,
            'email' => $request->email,
            'phone' => $request->whatsapp_number, // Using WhatsApp number as phone
            'whatsapp_number' => $request->whatsapp_number,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'status' => $request->status,
            'approved_at' => $request->status === 'approved' ? ($student->approved_at ?: now()) : null,
        ]);

        // Update user account - Auto-update password when phone number changes
        if ($student->user) {
            $oldPhone = $student->getOriginal('whatsapp_number');
            $newPhone = $request->whatsapp_number;
            
            $student->user->update([
                'name' => $student->full_name,
                'email' => $student->email,
                'password' => Hash::make($newPhone), // Always update to new phone number
                'is_active' => $request->status === 'approved'
            ]);
            
            // If phone number changed, log the password update
            if ($oldPhone !== $newPhone) {
                \Log::info("Student {$student->id} password updated due to phone number change from {$oldPhone} to {$newPhone}");
            }
        }

        $message = 'Student updated successfully!';
        if (isset($oldPhone) && isset($newPhone) && $oldPhone !== $newPhone) {
            $message .= ' Password has been updated to the new phone number.';
        }
        
        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }

    public function resetPassword(Student $student)
    {
        if (!$student->user) {
            return redirect()->back()
                ->with('error', 'Student does not have a user account.');
        }

        // Reset password to current phone number
        $student->user->update([
            'password' => Hash::make($student->whatsapp_number)
        ]);

        return redirect()->back()
            ->with('success', 'Password reset successfully! New password is: ' . $student->whatsapp_number);
    }

    public function destroy(Student $student)
    {
        // Check if student has any active enrollments or payments
        if ($student->enrollments()->where('status', 'active')->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete student with active enrollments. Please drop or remove enrollments first.');
        }

        if ($student->payments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete student with existing payments. Please handle payments first.');
        }

        try {
            DB::beginTransaction();

            // Delete dropped enrollments first
            $student->enrollments()->where('status', 'dropped')->delete();

            // Delete user account
            if ($student->user) {
                $student->user->delete();
            }

            // Delete student
            $student->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Force delete student with all related data
     */
    public function forceDestroy(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'confirmation' => 'required|in:REMOVE'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid confirmation. Please type REMOVE to confirm.');
        }

        // Delete all related data in correct order
        try {
            DB::beginTransaction();

            $enrollmentIds = $student->enrollments()->pluck('id');
            if ($enrollmentIds->isNotEmpty()) {
                CreditAllocation::whereIn('enrollment_id', $enrollmentIds)->delete();
            }

            // Delete credit transactions
            \App\Models\StudentCreditTransaction::where('student_id', $student->id)->delete();

            // Delete payment allocations first
            DB::table('payment_allocations')
                ->whereIn('payment_id', $student->payments()->pluck('id'))
                ->delete();

            // Delete payments
            $student->payments()->delete();

            // Delete assessment results
            $student->assessmentResults()->delete();

            // Delete student documents
            $student->documents()->delete();

            // Delete certificates
            $student->certificates()->delete();

            // Delete notifications
            $student->notifications()->delete();

            // Drop all enrollments
            $student->enrollments()->delete();

            // Delete user account
            if ($student->user) {
                $student->user->delete();
            }

            // Delete student
            $student->delete();

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student and all related data deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Remove student from specific enrollment
     */
    public function removeFromEnrollment(Enrollment $enrollment)
    {
        $paidAmount = (float) $enrollment->paid_amount;

        try {
            DB::beginTransaction();

            // Transfer paid amount to student credit before removing
            if ($paidAmount > 0) {
                $creditService = new \App\Services\StudentCreditService();
                $creditService->addCreditFromEnrollment($enrollment, 'enrollment_remove');
            }

            // Delete payment allocations for this enrollment
            DB::table('payment_allocations')
                ->whereIn('payment_id', $enrollment->payments()->pluck('id'))
                ->delete();

            // Delete payments for this enrollment
            $enrollment->payments()->delete();

            // Delete assessment results for this enrollment
            $enrollment->assessmentResults()->delete();

            // Delete certificates for this enrollment
            $enrollment->certificates()->delete();

            // Delete the enrollment
            $enrollment->delete();

            DB::commit();

            $msg = 'Student removed from enrollment successfully!';
            if ($paidAmount > 0) {
                $msg .= " ₹" . number_format($paidAmount, 0) . " transferred to student credit balance.";
            }

            return redirect()->back()
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error removing enrollment: ' . $e->getMessage());
        }
    }

    public function getBatchesByCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json([]);
        }

        $batches = Batch::where('course_id', $courseId)
            ->where('is_active', true)
            ->where(function ($query) {
                $gracePeriod = Carbon::today()->subDays(5);
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $gracePeriod);
            })
            ->orderByDesc('start_date')
            ->get(['id', 'batch_name', 'start_date', 'end_date', 'max_students']);

        return response()->json($batches->map(function ($batch) {
            return [
                'id' => $batch->id,
                'batch_name' => $batch->batch_name,
                'start_date' => $batch->start_date?->format('d-m-Y'),
                'end_date' => $batch->end_date?->format('d-m-Y'),
                'max_students' => $batch->max_students,
            ];
        }));
    }

    /**
     * Get course details including fees
     */
    public function getCourseDetails($courseId)
    {
        try {
            $course = Course::find($courseId);
            
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'course_fee' => $course->course_fee,
                    'registration_fee' => $course->registration_fee,
                    'assessment_fee' => $course->assessment_fee,
                    'total_fee' => $course->total_fee
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading course details'
            ], 500);
        }
    }

    /**
     * Student ID Card - HTML preview (on-screen)
     */
    public function idCardPreview(Student $student)
    {
        $student->load(['documents', 'enrollments.batch']);
        return view('admin.students.id-card-pdf', compact('student'));
    }

    /**
     * Student ID Card - PDF view
     */
    public function idCard(Student $student)
    {
        $student->load(['documents', 'enrollments.batch.course']);
        $pdf = Pdf::loadView('admin.students.id-card-pdf', compact('student'));
        $pdf->setPaper([0, 0, 242.65, 153.07]); // CR80: 85.6mm x 54mm (pt)
        return $pdf->stream('id-card-' . $student->id . '.pdf');
    }

    /**
     * Student ID Card - download
     */
    public function downloadIdCard(Student $student)
    {
        $student->load(['documents', 'enrollments.batch.course']);
        $pdf = Pdf::loadView('admin.students.id-card-pdf', compact('student'));
        $pdf->setPaper([0, 0, 242.65, 153.07]); // CR80: 85.6mm x 54mm
        return $pdf->download('id-card-' . $student->full_name . '.pdf');
    }

    /**
     * Upload a new document for a student
     */
    public function uploadDocument(Request $request, Student $student)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string|in:photo,aadhar,qualification_certificate',
                'file' => 'required|file',
                'crop_data' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }

            // Validate file type based on document type
            $documentType = $request->input('document_type');
            $file = $request->file('file');
            
            $allowedTypes = [
                'photo' => ['image/jpeg', 'image/jpg', 'image/png'],
                'aadhar' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'qualification_certificate' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            ];

            if (!in_array($file->getMimeType(), $allowedTypes[$documentType])) {
                return response()->json(['success' => false, 'message' => 'Invalid file type for ' . $documentType], 400);
            }

            // Parse crop data if provided
            $cropData = null;
            if ($request->has('crop_data') && $request->input('crop_data')) {
                $cropData = json_decode($request->input('crop_data'), true);
            }

            // Use DocumentUploadService to handle the upload
            $documentUploadService = new DocumentUploadService();
            $document = $documentUploadService->uploadDocument($file, $student->id, $documentType, $cropData);

            return response()->json(['success' => true, 'message' => 'Document uploaded successfully', 'document' => $document]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error uploading document: ' . $e->getMessage()], 500);
        }
    }

    public function viewDocument(StudentDocument $document, Request $request)
    {
        $path = $this->resolveDocumentPath($document);
        if (!$path) {
            \Log::warning('Document not found on storage', [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
                'storage_root' => Storage::disk('public')->path(''),
            ]);
            abort(404, 'Document not found. File may have been moved or deleted.');
        }

        $mimeType = 'application/octet-stream';
        if (function_exists('mime_content_type')) {
            $detected = @mime_content_type($path);
            if ($detected) {
                $mimeType = $detected;
            }
        }
        $headers = [
            'Cache-Control' => 'public, max-age=86400',
            'Content-Type' => $mimeType,
        ];

        if ($request->boolean('download')) {
            $filename = $document->original_name ?: basename($document->file_path);
            $headers['Content-Disposition'] = 'attachment; filename="' . addslashes($filename) . '"';
        }

        return response()->file($path, $headers);
    }

    /**
     * Resolve document file path - works on shared hosting where Storage::exists may fail
     */
    private function resolveDocumentPath(StudentDocument $document): ?string
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->path($document->file_path);
        }
        $fallbackPath = storage_path('app/public/' . ltrim($document->file_path, '/'));
        if (file_exists($fallbackPath)) {
            return $fallbackPath;
        }
        return null;
    }

    /**
     * Update an existing document for a student
     */
    public function updateDocument(Request $request, Student $student, $documentId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string|in:photo,aadhar,qualification_certificate',
                'file' => 'required|file',
                'crop_data' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }

            // Find the document
            $document = $student->documents()->findOrFail($documentId);

            // Validate file type based on document type
            $documentType = $request->input('document_type');
            $file = $request->file('file');
            
            $allowedTypes = [
                'photo' => ['image/jpeg', 'image/jpg', 'image/png'],
                'aadhar' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
                'qualification_certificate' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'],
            ];

            if (!in_array($file->getMimeType(), $allowedTypes[$documentType])) {
                return response()->json(['success' => false, 'message' => 'Invalid file type for ' . $documentType], 400);
            }

            // Parse crop data if provided
            $cropData = null;
            if ($request->has('crop_data') && $request->input('crop_data')) {
                $cropData = json_decode($request->input('crop_data'), true);
            }

            // Use DocumentUploadService to handle the update
            $documentUploadService = new DocumentUploadService();
            $updatedDocument = $documentUploadService->updateDocument($document, $file, $cropData);

            return response()->json(['success' => true, 'message' => 'Document updated successfully', 'document' => $updatedDocument]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating document: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove a document from a student
     */
    public function removeDocument(Student $student, $documentId)
    {
        try {
            $document = $student->documents()->findOrFail($documentId);
            
            $documentUploadService = new DocumentUploadService();
            $documentUploadService->deleteDocument($document);

            return response()->json(['success' => true, 'message' => 'Document removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error removing document: ' . $e->getMessage()], 500);
        }
    }
}
