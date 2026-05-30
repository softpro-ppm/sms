<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TrainingPartnerActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $scope = $request->input('role_scope'); // 'student' or 'staff' (reception/admin)

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Your account has been deactivated.'])
                    ->withInput($request->except('password'));
            }

            // Enforce that users sign in through the correct section
            if ($scope === 'student' && $user->role !== 'student') {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Please use the Reception / Admin login section for this account.'])
                    ->withInput($request->except('password'));
            }

            if ($scope === 'staff' && !in_array($user->role, ['admin', 'reception', 'super_admin'], true)) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['email' => 'Please use the Student login section for this account.'])
                    ->withInput($request->except('password'));
            }

            $request->session()->regenerate();

            if (in_array($user->role, ['admin', 'reception'], true)) {
                TrainingPartnerActivityLog::recordForUser(
                    $user,
                    'staff_login',
                    ucfirst($user->role).' signed in',
                    $request
                );
            }

            // Redirect based on role
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'reception' => redirect()->route('admin.dashboard'),
                'super_admin' => redirect()->route('admin.super.dashboard'),
                'student' => redirect()->route('student.dashboard'),
                default => redirect()->route('home')
            };
        }

        return redirect()->back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user && in_array($user->role, ['admin', 'reception'], true)) {
            TrainingPartnerActivityLog::recordForUser(
                $user,
                'staff_logout',
                ucfirst($user->role).' signed out',
                $request
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
