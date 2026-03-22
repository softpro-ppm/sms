@extends('layouts.admin')

@section('title', 'Training Partners')
@section('page-title', 'Training Partners')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Training Partners</h2>
            <p class="text-gray-600 mt-1">Manage HQ and franchise institutes</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.super.training-partners.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Add Partner
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">HQ</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['hq'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">Standard</p>
            <p class="text-2xl font-bold text-teal-600">{{ $stats['standard'] }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 w-48">
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Types</option>
                    <option value="HQ" {{ request('type') === 'HQ' ? 'selected' : '' }}>HQ</option>
                    <option value="STANDARD" {{ request('type') === 'STANDARD' ? 'selected' : '' }}>Standard</option>
                </select>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <select name="per_page" class="px-3 py-2 border border-gray-300 rounded-lg">
                    @foreach([10,20,50,100] as $n)
                        <option value="{{ $n }}" {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Partner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wallet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($partners as $partner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $partner->name }}</div>
                                <div class="text-sm text-gray-500">{{ $partner->code }}</div>
                            </div>
                        </td>
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
                        <td class="px-6 py-4 text-sm">₹{{ number_format($partner->wallet_balance, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $partner->users_count }} staff, {{ $partner->students_count }} students
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.super.training-partners.show', $partner) }}" class="text-primary-600 hover:text-primary-800" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.super.training-partners.edit', $partner) }}" class="text-blue-600 hover:text-blue-800" title="Edit"><i class="fas fa-edit"></i></a>
                                @if(!$partner->is_hq)
                                <form method="POST" action="{{ route('admin.super.training-partners.destroy', $partner) }}" class="inline" onsubmit="return confirm('Delete this partner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No training partners found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $partners->links() }}</div>
        @endif
    </div>
</div>
@endsection
