<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TrainingPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('public.partner-register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|alpha_dash|unique:training_partners,code',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        $data = $validator->validated();
        unset($data['logo']);

        $data['type'] = 'STANDARD';
        $data['status'] = 'pending';
        $data['wallet_balance'] = 0;
        $data['student_approval_deduction'] = 0; // Set by Super Admin on approval

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('partner-logos', 'public');
            $data['logo_path'] = $path;
        }

        TrainingPartner::create($data);

        return redirect()->route('partner.register.success')
            ->with('success', 'Your registration has been submitted. You will be notified once approved.');
    }

    public function success()
    {
        return view('public.partner-register-success');
    }
}
