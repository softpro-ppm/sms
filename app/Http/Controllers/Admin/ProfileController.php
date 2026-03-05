<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the admin/reception user's profile.
     */
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }
}
