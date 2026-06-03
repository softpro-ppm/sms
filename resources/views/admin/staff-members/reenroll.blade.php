@extends('layouts.admin')

@section('title', 'Re-enroll Staff Face')
@section('page-title', 'Re-enroll Staff Face')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="px-6 py-6">
            <a href="{{ route('admin.staff-members.show', $staffMember) }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                <i class="fas fa-arrow-left text-xs"></i> Back to profile
            </a>
            <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                <i class="fas fa-camera-rotate text-[10px]"></i>
                Face re-enrollment
            </div>
            <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Replace face samples for {{ $staffMember->name }}.</h2>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Capture at least 3 clear samples. Reception submissions go back to pending approval; admin submissions stay approved.</p>
        </div>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.staff-members.update-face', $staffMember) }}" id="staff-enrollment-form" class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @csrf
        @method('PATCH')
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Face samples</div>
            <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Capture new enrollment images</h3>
        </div>
        <div class="space-y-4 p-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                <video id="enroll-video" class="aspect-video w-full object-cover" playsinline autoplay muted></video>
                <canvas id="enroll-canvas" class="hidden"></canvas>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="capture-sample" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800" disabled>
                    <i class="fas fa-camera text-xs"></i>
                    Capture sample
                </button>
                <button type="button" id="clear-samples" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    <i class="fas fa-rotate text-xs"></i>
                    Clear
                </button>
                <span id="sample-count" class="text-sm font-medium text-slate-600">0 / 3 required</span>
            </div>
            <p id="face-status" class="text-sm text-slate-500">Loading face recognition models...</p>
            <div id="sample-preview" class="grid grid-cols-3 gap-3"></div>

            <input type="hidden" name="face_descriptors" id="face_descriptors">
            <input type="hidden" name="face_images" id="face_images">

            <button type="submit" id="submit-staff" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300" disabled>
                <i class="fas fa-paper-plane text-xs"></i>
                Save new face samples
            </button>
        </div>
    </form>
</div>

<script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('enroll-video');
    const canvas = document.getElementById('enroll-canvas');
    const status = document.getElementById('face-status');
    const captureButton = document.getElementById('capture-sample');
    const clearButton = document.getElementById('clear-samples');
    const submitButton = document.getElementById('submit-staff');
    const sampleCount = document.getElementById('sample-count');
    const preview = document.getElementById('sample-preview');
    const descriptorInput = document.getElementById('face_descriptors');
    const imageInput = document.getElementById('face_images');
    const form = document.getElementById('staff-enrollment-form');
    const descriptors = [];
    const images = [];
    let stream = null;

    const refreshState = () => {
        sampleCount.textContent = `${descriptors.length} / 3 required`;
        submitButton.disabled = descriptors.length < 3;
        descriptorInput.value = JSON.stringify(descriptors);
        imageInput.value = JSON.stringify(images);
    };

    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }
    };

    const load = async () => {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset('vendor/face-api/models') }}'),
            faceapi.nets.faceLandmark68TinyNet.loadFromUri('{{ asset('vendor/face-api/models') }}'),
            faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset('vendor/face-api/models') }}'),
        ]);

        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false,
        });
        video.srcObject = stream;
        captureButton.disabled = false;
        status.textContent = 'Camera ready. Capture front, slight left, and slight right samples.';
    };

    captureButton.addEventListener('click', async () => {
        if (descriptors.length >= 5) {
            status.textContent = 'Maximum 5 samples captured.';
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        const detection = await faceapi.detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks(true)
            .withFaceDescriptor();

        if (!detection) {
            status.textContent = 'No clear single face detected. Adjust lighting and try again.';
            return;
        }

        const image = canvas.toDataURL('image/jpeg', 0.92);
        descriptors.push(Array.from(detection.descriptor));
        images.push(image);

        const img = document.createElement('img');
        img.src = image;
        img.alt = 'Face sample';
        img.className = 'aspect-video rounded-2xl border border-slate-200 object-cover';
        preview.appendChild(img);

        status.textContent = 'Sample captured.';
        refreshState();
    });

    clearButton.addEventListener('click', () => {
        descriptors.splice(0, descriptors.length);
        images.splice(0, images.length);
        preview.innerHTML = '';
        status.textContent = 'Samples cleared.';
        refreshState();
    });

    form.addEventListener('submit', (event) => {
        if (descriptors.length < 3) {
            event.preventDefault();
            status.textContent = 'Capture at least 3 valid face samples.';
        } else {
            stopCamera();
        }
    });

    window.addEventListener('beforeunload', stopCamera);

    load().catch(() => {
        status.textContent = 'Camera/model loading failed. Check browser camera permission and reload.';
    });
});
</script>
@endsection
