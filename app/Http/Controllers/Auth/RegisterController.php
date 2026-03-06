<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SelfRegistrationAcknowledgementMail;
use App\Models\Student;
use App\Services\WhatsAppNotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhar_number' => 'required|string|size:12|regex:/^[0-9]{12}$/|unique:students,aadhar_number',
            'full_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'qualification' => 'required|string|in:ITI,Post Graduate,Below SSC,SSC,Intermediate,Graduation,B Tech,Diploma',
            'email' => 'required|string|email|max:255|unique:students,email|unique:users,email',
            'whatsapp_number' => 'required|string|size:10|regex:/^[0-9]{10}$/|unique:students,whatsapp_number',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('whatsapp_number'));
        }

        // Create student record
        $student = Student::create([
            'aadhar_number' => $request->aadhar_number,
            'full_name' => $request->full_name,
            'father_name' => $request->father_name,
            'gender' => $request->gender,
            'qualification' => $request->qualification,
            'email' => $request->email,
            'phone' => $request->whatsapp_number,
            'whatsapp_number' => $request->whatsapp_number,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'status' => 'pending',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->full_name,
            'email' => $student->email,
            'password' => Hash::make($student->whatsapp_number), // WhatsApp number as password
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => false, // Will be activated after admin approval
        ]);

        // Send self-registration acknowledgement email
        try {
            Mail::to($student->email)->send(new SelfRegistrationAcknowledgementMail($student));
        } catch (\Exception $e) {
            \Log::error('Self-registration email failed: ' . $e->getMessage());
        }
        try {
            app(WhatsAppNotificationService::class)->sendSelfRegistrationAck($student);
        } catch (\Exception $e) {
            \Log::error('Self-registration WhatsApp failed: ' . $e->getMessage());
        }

        return redirect()->to('/register/success');
    }

    public function success()
    {
        try {
            return view('auth.register-success');
        } catch (\Throwable $e) {
            \Log::error('Registration success page failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->view('auth.register-success-fallback', [], 200);
        }
    }
}
