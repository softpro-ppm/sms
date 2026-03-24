<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\PartnerRegistrationOtpMail;
use App\Models\TrainingPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PartnerRegistrationController extends Controller
{
    private const OTP_PURPOSE = 'partner_registration';
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_MAX_ATTEMPTS = 5;

    public function showRegistrationForm()
    {
        $locations = config('ap_locations');
        $logoConfig = config('logo');
        return view('public.partner-register', [
            'districts' => $locations['districts'],
            'mandalsByDistrict' => $locations['mandals_by_district'],
            'logoMaxKb' => $logoConfig['max_size_kb'],
            'logoMimes' => implode(', ', $logoConfig['mimes']),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Valid email is required.'], 422);
        }

        $email = strtolower(trim($request->email));
        $key = 'otp:' . self::OTP_PURPOSE . ':' . $email;

        $stored = Cache::get($key);
        if ($stored && ($stored['attempts'] ?? 0) >= self::OTP_MAX_ATTEMPTS) {
            return response()->json(['success' => false, 'message' => 'Too many attempts. Try again later.'], 429);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put($key, [
            'code' => $otp,
            'attempts' => 0,
        ], now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        try {
            Mail::to($email)->send(new PartnerRegistrationOtpMail($otp, (string) self::OTP_EXPIRY_MINUTES));
        } catch (\Throwable $e) {
            Log::error('Partner OTP send failed', ['email' => $email, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Email and 6-digit OTP are required.'], 422);
        }

        $email = strtolower(trim($request->email));
        $inputOtp = trim($request->otp);
        $key = 'otp:' . self::OTP_PURPOSE . ':' . $email;

        $stored = Cache::get($key);
        if (!$stored) {
            return response()->json(['success' => false, 'message' => 'OTP expired or invalid. Request a new one.'], 422);
        }

        $attempts = ($stored['attempts'] ?? 0) + 1;
        if ($attempts > self::OTP_MAX_ATTEMPTS) {
            Cache::forget($key);
            return response()->json(['success' => false, 'message' => 'Too many attempts. Request a new OTP.'], 422);
        }

        if (($stored['code'] ?? '') !== $inputOtp) {
            Cache::put($key, array_merge($stored, ['attempts' => $attempts]), now()->addMinutes(self::OTP_EXPIRY_MINUTES));
            return response()->json(['success' => false, 'message' => 'Invalid OTP.'] + ($attempts >= self::OTP_MAX_ATTEMPTS ? ['locked' => true] : []), 422);
        }

        Cache::forget($key);
        $request->session()->put('partner_email_verified', $email);

        return response()->json(['success' => true, 'message' => 'Email verified.']);
    }

    public function register(Request $request)
    {
        $logoConfig = config('logo');
        $logoMaxKb = $logoConfig['max_size_kb'];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => ['nullable', 'image', 'mimes:' . implode(',', $logoConfig['mimes']), 'max:' . $logoMaxKb],
            'district' => 'nullable|string|max:100',
            'mandal' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $verifiedEmail = $request->session()->get('partner_email_verified');
        if (!$verifiedEmail || $verifiedEmail !== strtolower(trim($request->contact_email))) {
            return redirect()->back()
                ->withErrors(['contact_email' => 'Email must be verified with OTP before submitting.'])
                ->withInput();
        }

        $data = $validator->validated();
        unset($data['logo']);

        $data['state'] = config('ap_locations.state', 'Andhra Pradesh');
        $data['type'] = 'STANDARD';
        $data['status'] = 'pending';
        $data['wallet_balance'] = 0;
        $data['student_approval_deduction'] = 0; // Set by Super Admin on approval

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('partner-logos', 'public');
            $data['logo_path'] = $path;
        }

        TrainingPartner::create($data);

        $request->session()->forget('partner_email_verified');

        return redirect()->route('partner.register.success')
            ->with('success', 'Your registration has been submitted. You will be notified once approved.');
    }

    public function success()
    {
        return view('public.partner-register-success');
    }
}
