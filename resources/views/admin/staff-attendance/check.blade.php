@extends('layouts.admin')

@section('title', 'Staff Attendance')
@section('page-title', 'Staff Attendance')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-camera text-[10px]"></i>
                    FRS attendance
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">{{ $user->name }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ now()->format('l, d M Y') }}</p>
            </div>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.staff-attendance.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-list-check text-xs"></i>
                    Attendance records
                </a>
            @endif
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Camera</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Live face capture</h3>
            </div>
            <div class="p-6">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                    <video id="attendance-video" class="aspect-video w-full object-cover" playsinline autoplay muted></video>
                    <canvas id="attendance-canvas" class="hidden"></canvas>
                </div>
                <p id="camera-status" class="mt-3 text-sm text-slate-500">Starting camera...</p>
            </div>
        </section>

        <section class="space-y-5">
            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Today</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Punch status</h3>
                </div>
                <div class="space-y-4 p-6">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Check in</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900">{{ $todayAttendance?->check_in_at?->format('h:i A') ?? 'Pending' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Check out</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900">{{ $todayAttendance?->check_out_at?->format('h:i A') ?? 'Pending' }}</p>
                        </div>
                    </div>

                    @if(!$user->face_enrolled_at)
                        <form method="POST" action="{{ route('admin.staff-attendance.enroll-face') }}" class="capture-form">
                            @csrf
                            <input type="hidden" name="face_image">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800">
                                <i class="fas fa-user-plus text-xs"></i>
                                Enroll face reference
                            </button>
                        </form>
                    @else
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            Face reference enrolled on {{ $user->face_enrolled_at->format('d M Y, h:i A') }}.
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <form method="POST" action="{{ route('admin.staff-attendance.check-in') }}" class="capture-form">
                                @csrf
                                <input type="hidden" name="face_image">
                                <button type="submit" @disabled($todayAttendance?->check_in_at) class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                                    <i class="fas fa-right-to-bracket text-xs"></i>
                                    Check in
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.staff-attendance.check-out') }}" class="capture-form">
                                @csrf
                                <input type="hidden" name="face_image">
                                <button type="submit" @disabled(!$todayAttendance?->check_in_at || $todayAttendance?->check_out_at) class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                                    <i class="fas fa-right-from-bracket text-xs"></i>
                                    Check out
                                </button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('admin.staff-attendance.enroll-face') }}" class="capture-form">
                            @csrf
                            <input type="hidden" name="face_image">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                <i class="fas fa-rotate text-xs"></i>
                                Update face reference
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('attendance-video');
    const canvas = document.getElementById('attendance-canvas');
    const status = document.getElementById('camera-status');

    navigator.mediaDevices?.getUserMedia({ video: { facingMode: 'user' }, audio: false })
        .then((stream) => {
            video.srcObject = stream;
            status.textContent = 'Camera ready.';
        })
        .catch(() => {
            status.textContent = 'Camera permission is required for attendance capture.';
        });

    document.querySelectorAll('.capture-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const input = form.querySelector('input[name="face_image"]');
            if (!video.videoWidth) {
                event.preventDefault();
                status.textContent = 'Camera is not ready yet.';
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            input.value = canvas.toDataURL('image/jpeg', 0.86);
        });
    });
});
</script>
@endsection
