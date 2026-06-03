@extends('layouts.admin')

@section('title', 'Staff Attendance Records')
@section('page-title', 'Staff Attendance')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-list-check text-[10px]"></i>
                    Staff attendance
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Automatic FRS attendance records.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review face match distance, time status, punch images, and location distance.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.staff-attendance.kiosk') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-camera text-xs"></i>
                    Open kiosk
                </a>
                <a href="{{ route('admin.staff-members.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-id-badge text-xs"></i>
                    Staff profiles
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.staff-attendance.index') }}" class="grid gap-4 border-b border-slate-200 px-6 py-5 md:grid-cols-[1fr_1fr_1.2fr_auto_auto] md:items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-slate-700">From</label>
                <input type="date" id="from" name="from" value="{{ request('from', $from->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-slate-700">To</label>
                <input type="date" id="to" name="to" value="{{ request('to', $to->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="staff_member_id" class="block text-sm font-medium text-slate-700">Staff</label>
                <select id="staff_member_id" name="staff_member_id" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
                    <option value="">All staff</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}" @selected((string) request('staff_member_id') === (string) $staff->id)>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.staff-attendance.export', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                <i class="fas fa-file-csv text-xs"></i>
                Export
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Staff</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check in</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check out</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Match</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Location</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Captures</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-900">{{ $record->attendance_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-900">{{ $record->staffMember?->name }}</p>
                                <p class="text-xs text-slate-500">{{ $record->staffMember?->staff_code ?: '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                {{ $record->check_in_at?->format('h:i A') ?? '-' }}
                                @if($record->check_in_status)
                                    <span class="block text-xs {{ $record->check_in_status === 'late' ? 'text-amber-700' : 'text-emerald-700' }}">{{ str_replace('_', ' ', ucfirst($record->check_in_status)) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                {{ $record->check_out_at?->format('h:i A') ?? '-' }}
                                @if($record->check_out_status)
                                    <span class="block text-xs {{ $record->check_out_status === 'early' ? 'text-amber-700' : 'text-emerald-700' }}">{{ ucfirst($record->check_out_status) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                In {{ $record->check_in_match_distance ?? '-' }}<br>
                                Out {{ $record->check_out_match_distance ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                In {{ $record->check_in_distance_meters !== null ? $record->check_in_distance_meters . 'm' : '-' }}<br>
                                Out {{ $record->check_out_distance_meters !== null ? $record->check_out_distance_meters . 'm' : '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($record->check_in_image_path)
                                        <a href="{{ Storage::disk('public')->url($record->check_in_image_path) }}" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100" title="Check-in capture">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @endif
                                    @if($record->check_out_image_path)
                                        <a href="{{ Storage::disk('public')->url($record->check_out_image_path) }}" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100" title="Check-out capture">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-500">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $records->links() }}</div>
        @endif
    </section>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Geofence</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Attendance location fence</h3>
        </div>
        <form method="POST" action="{{ route('admin.staff-attendance.geofence.update') }}" class="grid gap-4 p-6 md:grid-cols-[1fr_1fr_1fr_auto_auto] md:items-end">
            @csrf
            <div>
                <label for="attendance_latitude" class="block text-sm font-medium text-slate-700">Latitude</label>
                <input id="attendance_latitude" name="attendance_latitude" value="{{ old('attendance_latitude', $trainingPartner?->attendance_latitude) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="attendance_longitude" class="block text-sm font-medium text-slate-700">Longitude</label>
                <input id="attendance_longitude" name="attendance_longitude" value="{{ old('attendance_longitude', $trainingPartner?->attendance_longitude) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="attendance_radius_meters" class="block text-sm font-medium text-slate-700">Radius meters</label>
                <input id="attendance_radius_meters" name="attendance_radius_meters" type="number" min="20" max="1000" value="{{ old('attendance_radius_meters', $trainingPartner?->attendance_radius_meters ?? 100) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <button type="button" id="detect-attendance-location" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-100">
                <i class="fas fa-location-crosshairs text-xs"></i>
                Detect
            </button>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-save text-xs"></i>
                Save
            </button>
            <p id="attendance-location-status" class="md:col-span-5 text-sm text-slate-500">Use Detect on the attendance device, then save the centre geofence.</p>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const detectButton = document.getElementById('detect-attendance-location');
    const latitudeInput = document.getElementById('attendance_latitude');
    const longitudeInput = document.getElementById('attendance_longitude');
    const radiusInput = document.getElementById('attendance_radius_meters');
    const status = document.getElementById('attendance-location-status');

    detectButton?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            status.textContent = 'Location detection is not supported in this browser.';
            status.className = 'md:col-span-5 text-sm text-rose-700';
            return;
        }

        detectButton.disabled = true;
        detectButton.classList.add('opacity-70');
        status.textContent = 'Detecting current location... allow browser location permission.';
        status.className = 'md:col-span-5 text-sm text-blue-700';

        navigator.geolocation.getCurrentPosition((position) => {
            latitudeInput.value = position.coords.latitude.toFixed(7);
            longitudeInput.value = position.coords.longitude.toFixed(7);

            if (!radiusInput.value) {
                radiusInput.value = 100;
            }

            const accuracy = Math.round(position.coords.accuracy || 0);
            status.textContent = `Location detected. Accuracy approximately ${accuracy} meters. Review and click Save.`;
            status.className = 'md:col-span-5 text-sm text-emerald-700';
            detectButton.disabled = false;
            detectButton.classList.remove('opacity-70');
        }, () => {
            status.textContent = 'Could not detect location. Please allow permission or enter latitude/longitude manually.';
            status.className = 'md:col-span-5 text-sm text-rose-700';
            detectButton.disabled = false;
            detectButton.classList.remove('opacity-70');
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0,
        });
    });
});
</script>
@endsection
