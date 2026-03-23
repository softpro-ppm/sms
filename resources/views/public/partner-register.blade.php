@extends('layouts.login-split')

@section('content')
<div class="min-h-[calc(100vh-140px)] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-amber-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Partner Registration</h2>
                    <p class="text-sm text-gray-500">Register your institute as a Softpro Training Partner</p>
                </div>
            </div>

            <form method="POST" action="{{ route('partner.register') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Institute Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g. ABC Institute">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code <span class="text-red-500">*</span></label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required maxlength="20"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('code') border-red-500 @enderror"
                               placeholder="e.g. ABC001 (letters, numbers, hyphens)">
                        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Institute Logo (optional)</label>
                    <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800">
                    <p class="mt-1 text-xs text-gray-500">Max 2MB. JPEG, PNG only.</p>
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea id="address" name="address" rows="2" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('address') border-red-500 @enderror" placeholder="Full address">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <input type="text" id="district" name="district" value="{{ old('district') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mandal" class="block text-sm font-medium text-gray-700 mb-2">Mandal</label>
                        <input type="text" id="mandal" name="mandal" value="{{ old('mandal') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('mandal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                        <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}" maxlength="10"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('pincode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Person</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                            <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_name') border-red-500 @enderror">
                            @error('contact_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone <span class="text-red-500">*</span></label>
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_phone') border-red-500 @enderror"
                                   placeholder="10-digit mobile">
                            @error('contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_email') border-red-500 @enderror">
                            @error('contact_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Registration
                </button>
                <p class="text-center text-sm text-gray-500">
                    Already have an account? <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:text-amber-700">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
