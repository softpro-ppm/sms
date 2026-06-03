@extends('layouts.admin')

@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('admin.staff-members.show', $staffMember) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                    <i class="fas fa-arrow-left text-xs"></i> Back to profile
                </a>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-pen text-[10px]"></i>
                    Staff profile
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Edit {{ $staffMember->name }}.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Update staff details. Face samples stay unchanged for attendance matching.</p>
            </div>
        </div>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.staff-members.update', $staffMember) }}" class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @csrf
        @method('PUT')
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Details</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Basic staff details</h3>
        </div>
        <div class="grid gap-4 p-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-500">*</span></label>
                <input id="name" name="name" value="{{ old('name', $staffMember->name) }}" required class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="staff_code" class="block text-sm font-medium text-slate-700">Staff ID</label>
                <input id="staff_code" name="staff_code" value="{{ old('staff_code', $staffMember->staff_code) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone', $staffMember->phone) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $staffMember->email) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="joining_date" class="block text-sm font-medium text-slate-700">Joining date</label>
                <input id="joining_date" name="joining_date" type="date" value="{{ old('joining_date', $staffMember->joining_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="designation" class="block text-sm font-medium text-slate-700">Designation</label>
                <input id="designation" name="designation" value="{{ old('designation', $staffMember->designation) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="department" class="block text-sm font-medium text-slate-700">Department</label>
                <input id="department" name="department" value="{{ old('department', $staffMember->department) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <label class="md:col-span-2 flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $staffMember->is_active)) class="rounded border-slate-300 text-primary-600 focus:ring-primary-100">
                Active for attendance
            </label>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-5">
            <a href="{{ route('admin.staff-members.show', $staffMember) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-save text-xs"></i>
                Save changes
            </button>
        </div>
    </form>
</div>
@endsection
