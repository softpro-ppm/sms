@extends('layouts.auth')

@section('title', 'Registration Successful')
@section('heading', 'Registration Submitted!')
@section('subheading', 'Your account is pending approval')

@section('content')
<div class="text-center space-y-6">
    <!-- Success Icon -->
    <div class="mx-auto w-20 h-20 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center">
        <i class="fas fa-check text-white text-3xl"></i>
    </div>

    <!-- Success Message -->
    <div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Registration Successful!</h3>
        <p class="text-gray-600">
            Thank you for registering with our institute. Your account has been submitted for review.
        </p>
    </div>

    <!-- Status Card -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-yellow-800">Pending Approval</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    Your account will be activated within 24-48 hours after admin approval.
                </p>
            </div>
        </div>
    </div>

    <!-- What's Next -->
    <div class="bg-gray-50 rounded-lg p-6 text-left">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">What happens next?</h4>
        <div class="space-y-3">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 text-sm font-semibold">1</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Admin Review</p>
                    <p class="text-sm text-gray-600">Our admin will review your registration details</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 text-sm font-semibold">2</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Account Activation</p>
                    <p class="text-sm text-gray-600">You'll receive an email when your account is approved</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 text-sm font-semibold">3</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Start Learning</p>
                    <p class="text-sm text-gray-600">Login and enroll in courses to begin your journey</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="bg-primary-50 border border-primary-200 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-primary-900 mb-2">Need Help?</h4>
        <p class="text-sm text-primary-700 mb-3">
            If you have any questions about your registration, please contact us:
        </p>
        <div class="space-y-2 text-sm text-primary-700">
            <div class="flex items-center space-x-2">
                <i class="fas fa-phone w-4"></i>
                <span>+91 7799773656</span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-envelope w-4"></i>
                <span>skill.softpro@gmail.com</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('login') }}" 
           class="flex-1 bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 transition-colors duration-200 text-center">
            Go to Login
        </a>
        <a href="{{ route('home') }}" 
           class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors duration-200 text-center">
            Back to Home
        </a>
    </div>
</div>
@endsection
