<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForcePasswordController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        if (!$user->must_change_password) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.force-password');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user->must_change_password) {
            return redirect()->route('admin.dashboard');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password updated. You can continue using the dashboard.');
    }
}
