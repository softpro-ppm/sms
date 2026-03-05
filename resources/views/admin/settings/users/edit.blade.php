@extends('layouts.admin')

@section('title', 'Edit Staff User')
@section('page-title', 'Edit Staff User')

@section('content')
<div class="max-w-2xl">
    <div class="mb-4">
        <a href="{{ route('admin.settings.users.index') }}" class="text-primary-600 hover:text-primary-900">
            <i class="fas fa-arrow-left mr-1"></i> Back to Staff Users
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Edit {{ $user->name }}</h2>
            <p class="text-sm text-gray-600 mt-0.5">
                <a href="{{ route('admin.settings.users.change-password', $user) }}" class="text-amber-600 hover:text-amber-800 font-medium">
                    <i class="fas fa-key mr-1"></i> Change password
                </a>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.settings.users.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                <select name="role" id="role" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('role') border-red-500 @enderror">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="reception" {{ old('role', $user->role) === 'reception' ? 'selected' : '' }}>Reception</option>
                </select>
                @if(auth()->id() === $user->id)
                    <p class="mt-0.5 text-xs text-amber-600">Changing your own role may affect your access.</p>
                @endif
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                </label>
                <p class="mt-0.5 text-xs text-gray-500">Inactive users cannot log in.</p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 shadow-sm">
                    Update User
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
