@extends('layouts.admin')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Your Profile</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Name</label>
                <p class="mt-1 text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Email</label>
                <p class="mt-1 text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Role</label>
                <p class="mt-1 text-gray-900">{{ ucfirst($user->role) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
