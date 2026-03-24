@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Platform Overview</h2>
                <p class="text-purple-100 text-lg">Manage training partners and platform settings.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-shield-alt text-purple-600 text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Partners</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_partners'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Partners</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_partners'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">HQ Partners</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['hq_partners'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Standard Partners</p>
                    <p class="text-2xl font-bold text-teal-600">{{ $stats['standard_partners'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Staff</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['total_staff'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Partners -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Training Partners</h3>
            <a href="{{ route('admin.super.training-partners.index') }}"
               class="text-primary-600 hover:text-primary-800 font-medium text-sm">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPartners as $partner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $partner->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $partner->code ?: '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $partner->type === 'HQ' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $partner->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $partner->status === 'active' ? 'bg-green-100 text-green-800' : ($partner->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.super.training-partners.show', $partner) }}"
                               class="text-primary-600 hover:text-primary-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No training partners yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
