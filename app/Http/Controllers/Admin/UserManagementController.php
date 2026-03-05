<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Get staff users (admin and reception only).
     */
    protected function getStaffUsers()
    {
        return User::whereIn('role', ['admin', 'reception'])
            ->whereNull('student_id')
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    }

    /**
     * Display list of staff users.
     */
    public function index()
    {
        $users = $this->getStaffUsers();
        return view('admin.settings.users.index', compact('users'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.settings.users.create');
    }

    /**
     * Store new staff user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,reception'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.settings.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(User $user)
    {
        $this->ensureStaffUser($user);
        return view('admin.settings.users.edit', compact('user'));
    }

    /**
     * Update staff user.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureStaffUser($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,reception'],
            'is_active' => ['boolean'],
        ]);

        // Prevent deactivating the last admin
        if ($user->is_admin && isset($validated['is_active']) && !$validated['is_active']) {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['is_active' => 'Cannot deactivate the only admin.']);
            }
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.settings.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show change password form.
     */
    public function showChangePassword(User $user)
    {
        $this->ensureStaffUser($user);
        return view('admin.settings.users.change-password', compact('user'));
    }

    /**
     * Process password change.
     */
    public function changePassword(Request $request, User $user)
    {
        $this->ensureStaffUser($user);

        $isChangingSelf = $user->id === auth()->id();

        if ($isChangingSelf) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        } else {
            $validated = $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        $message = $isChangingSelf ? 'Your password has been changed.' : 'Password changed successfully.';
        return redirect()->route('admin.settings.users.index')
            ->with('success', $message);
    }

    /**
     * Ensure user is a staff member (admin or reception).
     */
    protected function ensureStaffUser(User $user): void
    {
        if (!in_array($user->role, ['admin', 'reception']) || $user->student_id !== null) {
            abort(404);
        }
    }
}
