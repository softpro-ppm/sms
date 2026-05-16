<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentVerificationController extends Controller
{
    /**
     * Verify student by enrollment number (public, no login).
     * Used as QR code target: /verify/{enrollment_no}
     */
    public function verifyByEnrollment(string $enrollment_no)
    {
        $enrollment = Enrollment::where('enrollment_number', $enrollment_no)
            ->with(['student.documents', 'batch.course'])
            ->first();

        if (!$enrollment || !$enrollment->student) {
            return view('public.verify-not-found', ['enrollment_no' => $enrollment_no]);
        }

        $student = $enrollment->student;
        $student->load([
            'enrollments.batch.course',
            'documents',
        ]);

        return view('public.student-verification-result', compact('student'));
    }

    /**
     * Serve student photo for verification page (public, no login).
     */
    public function verifyPhoto(string $enrollment_no)
    {
        $enrollment = Enrollment::where('enrollment_number', $enrollment_no)
            ->with(['student.documents'])
            ->first();

        if (!$enrollment || !$enrollment->student) {
            abort(404);
        }

        $photoDoc = $enrollment->student->documents()->where('document_type', 'photo')->first();
        if (!$photoDoc || !Storage::disk('public')->exists($photoDoc->file_path)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($photoDoc->file_path);
        $mimeType = Storage::disk('public')->mimeType($photoDoc->file_path) ?: 'image/jpeg';

        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
            'Content-Type' => $mimeType,
        ]);
    }

    /**
     * Show result for a specific student (when picked from multiple matches).
     */
    public function showResult(Student $student)
    {
        $candidates = session('verify_candidates', []);
        if (! in_array($student->id, $candidates, true)) {
            return redirect()->route('verify.index')
                ->with('error', 'Please search again to verify your details.');
        }

        $student->load([
            'enrollments.batch.course',
            'documents',
        ]);

        return view('public.student-verification-result', compact('student'));
    }

    /**
     * Show the student verification search page.
     */
    public function index()
    {
        return view('public.student-verification');
    }

    /**
     * Search and display student verification details.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_term' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $searchTerm = trim($request->search_term);

        $students = Student::where(function ($query) use ($searchTerm) {
            $query->where('full_name', 'like', "%{$searchTerm}%")
                ->orWhere('phone', 'like', "%{$searchTerm}%")
                ->orWhere('whatsapp_number', 'like', "%{$searchTerm}%")
                ->orWhere('aadhar_number', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
        })
            ->orWhereHas('enrollments', function ($query) use ($searchTerm) {
                $query->where('enrollment_number', 'like', "%{$searchTerm}%");
            })
            ->with([
                'enrollments.batch.course',
            ])
            ->get()
            ->unique('id')
            ->values();

        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No student found with the provided information. Please check your details and try again.')
                ->withInput();
        }

        if ($students->count() === 1) {
            $student = $students->first();
            $student->loadMissing(['documents']);

            return view('public.student-verification-result', compact('student'));
        }

        session(['verify_candidates' => $students->pluck('id')->toArray()]);

        return view('public.verify-multiple', compact('students'));
    }
}
