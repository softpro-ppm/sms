@extends('layouts.admin')

@section('title', 'Staff Users')
@section('page-title', 'Staff Users')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-user-shield text-[10px]"></i>
                    Staff users
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Manage admin and reception accounts.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Create staff access, update account details, and keep operational roles clear across the centre.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Back to settings
                </a>
                <a href="{{ route('admin.staff-attendance.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                    <i class="fas fa-list-check text-xs"></i>
                    Attendance
                </a>
                <a href="{{ route('admin.staff-members.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">
                    <i class="fas fa-id-badge text-xs"></i>
                    Staff profiles
                </a>
                <a href="{{ route('admin.settings.users.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-user-plus text-xs"></i>
                    Add user
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">User records</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Current staff accounts</h3>
            </div>
            <div class="text-sm text-slate-500">{{ $users->count() }} staff users</div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Name</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Email</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Role</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Face</th>
                    <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                    <th class="px-5 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="font-medium text-slate-900">{{ $user->name }}</span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $user->email }}</td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                            {{ $user->role === 'admin' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        @if($user->face_enrolled_at)
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Enrolled</span>
                        @else
                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Pending</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        @if($user->is_active)
                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-right">
                        <a href="{{ route('admin.settings.users.edit', $user) }}" 
                           class="mr-2 inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600 transition hover:border-blue-300 hover:bg-blue-100"
                           title="Edit user">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('admin.settings.users.change-password', $user) }}" 
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-amber-600 transition hover:border-amber-300 hover:bg-amber-100"
                           title="Change password">
                            <i class="fas fa-key"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-500">No staff users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
