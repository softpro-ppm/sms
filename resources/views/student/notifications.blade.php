@extends('layouts.student')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6 sm:px-8 sm:py-7 border-b border-gray-100">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">
                <i class="fas fa-bell text-[11px]"></i>
                Student notifications
            </span>
            <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-semibold text-gray-950">Manage browser alerts</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Choose which student app notifications you want to receive after installing the PWA and enabling browser notifications.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-sm text-gray-600">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Includes</p>
                    <p class="mt-2">Fee reminders, payment updates, exam alerts, and certificate notices.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('student.notifications.update') }}" class="px-6 py-6 sm:px-8 sm:py-7 space-y-4">
            @csrf

            @foreach($notificationTypes as $type => $meta)
                @php $pref = $preferences[$type] ?? null; @endphp
                <div class="flex items-start justify-between gap-4 rounded-2xl border border-gray-200 bg-gray-50/60 px-5 py-4">
                    <div class="max-w-2xl">
                        <h3 class="text-sm font-semibold text-gray-950">{{ $meta['title'] }}</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-600">{{ $meta['description'] }}</p>
                    </div>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                        <span>{{ ($pref?->push_enabled ?? true) ? 'Enabled' : 'Disabled' }}</span>
                        <input
                            type="checkbox"
                            name="push_enabled[{{ $type }}]"
                            value="1"
                            @checked($pref?->push_enabled ?? true)
                            class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        >
                    </label>
                </div>
            @endforeach

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center rounded-xl bg-primary-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-primary-700">
                    <i class="fas fa-save mr-2 text-xs"></i>
                    Save preferences
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
