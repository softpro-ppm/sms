@extends('layouts.admin')

@section('title', 'Attendance Kiosk')
@section('page-title', 'Attendance Kiosk')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-camera text-[10px]"></i>
                    Automatic FRS kiosk
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Staff attendance kiosk</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Check-in window: 6:00 AM-10:00 AM. On-time until 9:30 AM. Check-out window: 4:30 PM-9:00 PM.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.staff-members.create') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-user-plus text-xs"></i>
                    Register staff
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.staff-attendance.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-list-check text-xs"></i>
                        Records
                    </a>
                @endif
            </div>
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Live camera</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Auto recognition</h3>
            </div>
            <div class="p-6">
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                    <video id="kiosk-video" class="aspect-video w-full object-cover" playsinline autoplay muted></video>
                    <canvas id="kiosk-canvas" class="hidden"></canvas>
                </div>
                <p id="kiosk-status" class="mt-3 text-sm text-slate-500">Loading face recognition models...</p>
            </div>
        </section>

        <section class="space-y-5">
            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Match</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Current result</h3>
                </div>
                <div class="space-y-4 p-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Detected staff</p>
                        <p id="matched-name" class="mt-2 text-2xl font-semibold text-slate-900">Waiting...</p>
                        <p id="matched-distance" class="mt-1 text-sm text-slate-500">No match yet</p>
                    </div>
                    <div id="punch-message" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                        Stand in front of the camera. Attendance will be marked automatically.
                    </div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Approved staff loaded: {{ $staffMembers->count() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const staffMembers = @json($staffMembers);
    const settings = @json($settings);
    const video = document.getElementById('kiosk-video');
    const canvas = document.getElementById('kiosk-canvas');
    const status = document.getElementById('kiosk-status');
    const matchedName = document.getElementById('matched-name');
    const matchedDistance = document.getElementById('matched-distance');
    const punchMessage = document.getElementById('punch-message');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let locationSnapshot = {};
    let isPunching = false;
    let lastPunchAt = 0;
    let matcher = null;

    const setMessage = (message, mode = 'neutral') => {
        const colors = {
            neutral: 'border-slate-200 bg-white text-slate-600',
            success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
            error: 'border-rose-200 bg-rose-50 text-rose-800',
            warning: 'border-amber-200 bg-amber-50 text-amber-800',
        };
        punchMessage.className = `rounded-2xl px-4 py-3 text-sm ${colors[mode] || colors.neutral}`;
        punchMessage.textContent = message;
    };

    const getLocation = () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition((position) => {
            locationSnapshot = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
            };
        }, () => {
            setMessage('Location permission is unavailable. Geofence will be enforced if configured.', 'warning');
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
    };

    const captureImage = () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.86);
    };

    const punch = async (match) => {
        if (isPunching || Date.now() - lastPunchAt < 12000) return;
        isPunching = true;
        lastPunchAt = Date.now();

        try {
            const response = await fetch('{{ route('admin.staff-attendance.kiosk.punch') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    staff_member_id: match.label,
                    face_image: captureImage(),
                    match_distance: match.distance,
                    ...locationSnapshot,
                }),
            });

            const payload = await response.json();
            if (!response.ok) {
                const message = payload.message || payload.errors?.attendance?.[0] || 'Attendance could not be marked.';
                setMessage(message, 'error');
                return;
            }

            setMessage(payload.message, 'success');
        } catch (error) {
            setMessage('Network error while marking attendance.', 'error');
        } finally {
            isPunching = false;
        }
    };

    const scan = async () => {
        if (!matcher || isPunching || !video.videoWidth) return;
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks(true)
            .withFaceDescriptor();

        if (!detection) {
            matchedName.textContent = 'Waiting...';
            matchedDistance.textContent = 'No clear face detected';
            return;
        }

        const match = matcher.findBestMatch(detection.descriptor);
        const staff = staffMembers.find((item) => String(item.id) === String(match.label));

        if (!staff || match.distance > settings.max_match_distance) {
            matchedName.textContent = 'Unknown';
            matchedDistance.textContent = `Distance ${match.distance.toFixed(3)}`;
            return;
        }

        matchedName.textContent = staff.name;
        matchedDistance.textContent = `Distance ${match.distance.toFixed(3)}`;
        await punch(match);
    };

    const load = async () => {
        if (!staffMembers.length) {
            status.textContent = 'No approved staff with face samples found.';
            return;
        }

        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset('vendor/face-api/models') }}'),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri('{{ asset('vendor/face-api/models') }}'),
            faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset('vendor/face-api/models') }}'),
        ]);

        const labeledDescriptors = staffMembers.map((staff) => new faceapi.LabeledFaceDescriptors(
            String(staff.id),
            staff.descriptors.map((descriptor) => new Float32Array(descriptor))
        ));
        matcher = new faceapi.FaceMatcher(labeledDescriptors, settings.max_match_distance);

        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
        video.srcObject = stream;
        getLocation();
        status.textContent = 'Kiosk ready. Recognition is running automatically.';
        setInterval(scan, 2500);
        setInterval(getLocation, 60000);
    };

    load().catch(() => {
        status.textContent = 'Camera/model loading failed. Check permission and reload.';
        setMessage('Kiosk could not start.', 'error');
    });
});
</script>
@endsection
