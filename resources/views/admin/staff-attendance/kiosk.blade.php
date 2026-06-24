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
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Automatic check-in runs from 6:00 AM to 10:00 AM. Check-out runs from 4:30 PM to 9:00 PM. Camera pauses when this tab is not active.</p>
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
                <div class="relative mx-auto max-w-[34rem] overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 sm:max-w-none">
                    <video id="kiosk-video" class="aspect-[3/4] w-full object-contain sm:aspect-video" playsinline autoplay muted></video>
                    <canvas id="kiosk-canvas" class="hidden"></canvas>
                    <button type="button" id="switch-camera" class="absolute right-3 top-3 inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/20 bg-slate-950/70 text-white shadow-lg backdrop-blur transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-white/70" title="Switch camera" aria-label="Switch camera">
                        <i class="fas fa-camera-rotate text-base"></i>
                    </button>
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

<div id="attendance-popup" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">
    <div class="w-full max-w-xl overflow-hidden rounded-[28px] border border-emerald-200 bg-white shadow-2xl">
        <div class="bg-emerald-600 px-6 py-5 text-white">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                    <i class="fas fa-check text-3xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-100">Attendance marked</p>
                    <h3 id="popup-staff" class="mt-1 text-3xl font-bold tracking-tight">Staff</h3>
                </div>
            </div>
        </div>
        <div class="space-y-4 p-6">
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Action</p>
                    <p id="popup-action" class="mt-1 text-xl font-semibold text-slate-900">Recorded</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Time</p>
                    <p id="popup-time" class="mt-1 text-xl font-semibold text-slate-900">--:--</p>
                </div>
            </div>
            <p id="popup-message" class="text-base leading-7 text-slate-700">Attendance recorded successfully.</p>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Location</p>
                <p id="popup-location" class="mt-1 text-sm font-medium text-slate-800">Not checked</p>
            </div>
            <div class="flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                <span class="text-sm font-medium text-emerald-800">Auto closing</span>
                <span id="popup-countdown" class="text-sm font-semibold text-emerald-900">4s</span>
            </div>
        </div>
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
    const popup = document.getElementById('attendance-popup');
    const popupStaff = document.getElementById('popup-staff');
    const popupAction = document.getElementById('popup-action');
    const popupTime = document.getElementById('popup-time');
    const popupMessage = document.getElementById('popup-message');
    const popupLocation = document.getElementById('popup-location');
    const popupCountdown = document.getElementById('popup-countdown');
    const switchCameraButton = document.getElementById('switch-camera');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let locationSnapshot = {};
    let isPunching = false;
    let popupOpen = false;
    let matcher = null;
    let popupTimer = null;
    let scanTimer = null;
    let locationTimer = null;
    let activeStream = null;
    let isScanning = false;
    let cameraFacingMode = 'user';
    let confirmedMatch = null;
    const staffCooldowns = new Map();
    const cooldownMs = 120000;
    const detectorOptions = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.45 });
    const requiredStableMatches = 3;
    const minimumDistanceGap = 0.035;
    const strictMatchDistance = settings.max_match_distance;
    const strongSingleMatchDistance = 0.38;
    const supportMatchDistance = 0.50;
    const requiredSupportingSamples = 2;

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

    const playSuccessTone = () => {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            const audio = new AudioContext();
            const gain = audio.createGain();
            gain.gain.value = 0.08;
            gain.connect(audio.destination);

            [660, 880].forEach((frequency, index) => {
                const oscillator = audio.createOscillator();
                oscillator.frequency.value = frequency;
                oscillator.type = 'sine';
                oscillator.connect(gain);
                oscillator.start(audio.currentTime + index * 0.12);
                oscillator.stop(audio.currentTime + index * 0.12 + 0.1);
            });
        } catch (error) {
            // Audio feedback is optional; browsers may block it until user interaction.
        }
    };

    const showAttendancePopup = (payload, match) => {
        window.clearInterval(popupTimer);
        popupStaff.textContent = payload.staff || 'Staff';
        popupAction.textContent = payload.action === 'check_in' ? 'Check-in' : 'Check-out';
        popupTime.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        popupMessage.textContent = payload.message || 'Attendance recorded successfully.';
        popupLocation.textContent = payload.location?.configured
            ? `${payload.location.inside ? 'Inside location fence' : 'Outside location fence'}${payload.location.meters !== null ? ` (${payload.location.meters}m)` : ''}`
            : 'Geofence not configured or location unavailable';

        let remaining = 4;
        popupCountdown.textContent = `${remaining}s`;
        popup.classList.remove('hidden');
        popup.classList.add('flex');
        popupOpen = true;
        playSuccessTone();

        popupTimer = window.setInterval(() => {
            remaining -= 1;
            popupCountdown.textContent = `${Math.max(remaining, 0)}s`;
            if (remaining <= 0) {
                window.clearInterval(popupTimer);
                popup.classList.add('hidden');
                popup.classList.remove('flex');
                popupOpen = false;
                staffCooldowns.set(String(match.label), Date.now() + cooldownMs);
            }
        }, 1000);
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

    const captureImage = (detection = null) => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.86);
    };

    const captureAttendanceImage = (detection) => {
        if (!detection?.detection?.box || !video.videoWidth) {
            return captureImage();
        }

        const source = document.createElement('canvas');
        source.width = video.videoWidth;
        source.height = video.videoHeight;
        source.getContext('2d').drawImage(video, 0, 0, source.width, source.height);

        const box = detection.detection.box;
        const cropWidth = Math.min(source.width, Math.max(box.width * 3.2, 420));
        const cropHeight = Math.min(source.height, Math.max(box.height * 4.1, 560));
        const centerX = box.x + box.width / 2;
        const centerY = box.y + box.height * 1.2;
        const cropX = Math.max(0, Math.min(source.width - cropWidth, centerX - cropWidth / 2));
        const cropY = Math.max(0, Math.min(source.height - cropHeight, centerY - cropHeight / 2));

        canvas.width = 720;
        canvas.height = 900;
        const context = canvas.getContext('2d');
        context.fillStyle = '#f8fafc';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(source, cropX, cropY, cropWidth, cropHeight, 0, 0, canvas.width, canvas.height);

        return canvas.toDataURL('image/jpeg', 0.92);
    };

    const faceLooksUsable = (detection) => {
        const box = detection?.detection?.box;
        if (!box || !video.videoWidth || !video.videoHeight) return false;

        const faceRatio = box.width / video.videoWidth;
        const centerX = box.x + box.width / 2;
        const centerY = box.y + box.height / 2;
        const isCentered = centerX > video.videoWidth * 0.22
            && centerX < video.videoWidth * 0.78
            && centerY > video.videoHeight * 0.16
            && centerY < video.videoHeight * 0.78;

        return faceRatio >= 0.13 && isCentered;
    };

    const stopKioskCamera = () => {
        window.clearInterval(scanTimer);
        window.clearInterval(locationTimer);
        scanTimer = null;
        locationTimer = null;

        if (activeStream) {
            activeStream.getTracks().forEach((track) => track.stop());
            activeStream = null;
        }

        video.srcObject = null;
        resetStableMatch();
        status.textContent = 'Kiosk paused. Return to this tab to restart camera.';
    };

    const startKioskCamera = async () => {
        if (document.hidden || activeStream || !matcher) return;
        const portraitCamera = window.matchMedia('(max-width: 640px)').matches;

        const videoConstraints = {
            facingMode: { ideal: cameraFacingMode },
            width: { ideal: portraitCamera ? 640 : 1280 },
            height: { ideal: portraitCamera ? 480 : 720 },
        };

        try {
            activeStream = await navigator.mediaDevices.getUserMedia({
                video: videoConstraints,
                audio: false,
            });
        } catch (error) {
            cameraFacingMode = cameraFacingMode === 'user' ? 'environment' : 'user';
            activeStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    ...videoConstraints,
                    facingMode: { ideal: cameraFacingMode },
                },
                audio: false,
            });
        }

        video.srcObject = activeStream;
        getLocation();
        status.textContent = `Kiosk ready. ${cameraFacingMode === 'user' ? 'Front' : 'Back'} camera is running automatically.`;
        scanTimer = window.setInterval(scan, 1100);
        locationTimer = window.setInterval(getLocation, 60000);
    };

    const punch = async (match, detection) => {
        const staffCooldownUntil = staffCooldowns.get(String(match.label)) || 0;
        if (isPunching || popupOpen || Date.now() < staffCooldownUntil) return;
        isPunching = true;

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
                    face_image: captureAttendanceImage(detection),
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
            showAttendancePopup(payload, match);
        } catch (error) {
            setMessage('Network error while marking attendance.', 'error');
        } finally {
            isPunching = false;
        }
    };

    const findBestStaffMatch = (descriptor) => {
        const staffDistances = staffMembers.map((staff) => {
            const distances = staff.descriptors.map((storedDescriptor) => (
                faceapi.euclideanDistance(descriptor, new Float32Array(storedDescriptor))
            )).sort((first, second) => first - second);

            return {
                label: String(staff.id),
                staff,
                distance: distances[0] || Number.POSITIVE_INFINITY,
                supportCount: distances.filter((distance) => distance <= supportMatchDistance).length,
            };
        }).sort((first, second) => first.distance - second.distance);

        const best = staffDistances[0] || null;
        const second = staffDistances[1] || null;
        const gap = best && second ? second.distance - best.distance : 1;

        return { best, second, gap };
    };

    const rememberStableMatch = (candidate) => {
        if (!confirmedMatch || confirmedMatch.label !== candidate.label) {
            confirmedMatch = { label: candidate.label, count: 1, distance: candidate.distance };
        } else {
            confirmedMatch.count += 1;
            confirmedMatch.distance = Math.min(confirmedMatch.distance, candidate.distance);
        }

        return confirmedMatch.count >= requiredStableMatches;
    };

    const resetStableMatch = () => {
        confirmedMatch = null;
    };

    const scan = async () => {
        if (!matcher || isPunching || popupOpen || !video.videoWidth || isScanning) return;
        isScanning = true;

        try {
            const detection = await faceapi.detectSingleFace(video, detectorOptions)
                .withFaceLandmarks(true)
                .withFaceDescriptor();

            if (!detection) {
                matchedName.textContent = 'Waiting...';
                matchedDistance.textContent = 'No clear face detected';
                resetStableMatch();
                return;
            }

            if (!faceLooksUsable(detection)) {
                matchedName.textContent = 'Adjust position';
                matchedDistance.textContent = 'Move closer and face the camera';
                setMessage('Move closer, keep face centered, and look at the camera.', 'warning');
                resetStableMatch();
                return;
            }

            const matchResult = findBestStaffMatch(detection.descriptor);
            const match = matchResult.best;
            const staff = match?.staff;

            if (!staff || match.distance > strictMatchDistance) {
                matchedName.textContent = 'Unknown';
                matchedDistance.textContent = match ? `Distance ${match.distance.toFixed(3)} · low confidence` : 'No match';
                setMessage('Face not recognized with safe confidence. Please stand clearly in front or re-enroll this staff.', 'warning');
                resetStableMatch();
                return;
            }

            const hasStrongSingleMatch = match.distance <= strongSingleMatchDistance;
            const hasSupportingSamples = match.supportCount >= requiredSupportingSamples;

            if (!hasStrongSingleMatch && !hasSupportingSamples) {
                matchedName.textContent = 'Need clearer match';
                matchedDistance.textContent = `${staff.name} ${match.distance.toFixed(3)} · sample support ${match.supportCount}/${requiredSupportingSamples}`;
                setMessage('Face is close, but not enough enrolled samples agree. Re-enroll this staff with clearer front/left/right photos.', 'warning');
                resetStableMatch();
                return;
            }

            if (matchResult.gap < minimumDistanceGap && !hasStrongSingleMatch) {
                matchedName.textContent = 'Ambiguous match';
                matchedDistance.textContent = `${staff.name} ${match.distance.toFixed(3)} / next ${matchResult.second.distance.toFixed(3)}`;
                setMessage('Face match is too close to another staff. Improve lighting or re-enroll clearer samples.', 'warning');
                resetStableMatch();
                return;
            }

            matchedName.textContent = staff.name;
            matchedDistance.textContent = `Distance ${match.distance.toFixed(3)} · samples ${match.supportCount} · confirming ${confirmedMatch?.label === match.label ? Math.min(confirmedMatch.count + 1, requiredStableMatches) : 1}/${requiredStableMatches}`;

            if (!rememberStableMatch(match)) {
                return;
            }

            await punch({ label: match.label, distance: confirmedMatch.distance }, detection);
            resetStableMatch();
        } finally {
            isScanning = false;
        }
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

        await startKioskCamera();
    };

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopKioskCamera();
        } else {
            startKioskCamera().catch(() => {
                status.textContent = 'Camera restart failed. Reload the kiosk page.';
            });
        }
    });

    switchCameraButton?.addEventListener('click', async () => {
        cameraFacingMode = cameraFacingMode === 'user' ? 'environment' : 'user';
        stopKioskCamera();
        status.textContent = `Switching to ${cameraFacingMode === 'user' ? 'front' : 'back'} camera...`;

        try {
            await startKioskCamera();
            setMessage(`${cameraFacingMode === 'user' ? 'Front' : 'Back'} camera selected.`, 'neutral');
        } catch (error) {
            status.textContent = 'Camera switch failed. Please allow camera permission and try again.';
            setMessage('Camera switch failed on this device/browser.', 'error');
        }
    });

    window.addEventListener('blur', stopKioskCamera);
    window.addEventListener('focus', () => {
        startKioskCamera().catch(() => {
            status.textContent = 'Camera restart failed. Reload the kiosk page.';
        });
    });

    window.addEventListener('beforeunload', stopKioskCamera);

    load().catch(() => {
        status.textContent = 'Camera/model loading failed. Check permission and reload.';
        setMessage('Kiosk could not start.', 'error');
    });
});
</script>
@endsection
