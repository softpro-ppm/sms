@extends('layouts.login-split')

@section('content')
<div class="min-h-[calc(100vh-140px)] py-16 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Registration Submitted</h2>
            <p class="text-gray-600 mb-6">
                {{ session('success', 'Your training partner registration has been submitted successfully.') }}
            </p>
            <p class="text-sm text-gray-500 mb-4">
                Our team will review your application and notify you at the contact email provided. You may receive a follow-up call for verification.
            </p>
            <p class="text-sm text-gray-600 mb-6 rounded-lg bg-gray-50 border border-gray-100 px-4 py-3 text-left">
                After approval, your centre gets a <strong>wallet</strong> for a <strong>per-student approval charge</strong> (set by head office). Course fees from students are handled separately inside the student system—this is not automatic commission on every payment.
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-amber-400 hover:bg-amber-500 text-black font-semibold rounded-lg transition-colors">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
