<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form (staff only - Reception/Admin).
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     * Only for admin/reception (staff) accounts.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && !in_array($user->role, ['admin', 'reception'], true)) {
            return back()->withErrors([
                'email' => 'Password reset is only available for staff accounts (Reception/Admin). Please use the Student login section for student accounts.',
            ])->withInput($request->only('email'));
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'If that email is registered as a staff account, you will receive a password reset link shortly.');
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
