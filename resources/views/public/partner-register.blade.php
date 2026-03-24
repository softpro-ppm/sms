@extends('layouts.login-split')

@section('content')
<div class="min-h-[calc(100vh-140px)] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-amber-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Partner Registration</h2>
                    <p class="text-sm text-gray-500">Register your institute as a Softpro Training Partner</p>
                </div>
            </div>

            <form method="POST" action="{{ route('partner.register') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Institute Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('name') border-red-500 @enderror"
                           placeholder="e.g. ABC Institute">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Institute Logo (optional)</label>
                    <div class="flex flex-col sm:flex-row gap-4 items-start">
                        <div class="w-24 h-24 rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden" id="logoPreviewWrap">
                            <span id="logoPlaceholder" class="text-gray-400 text-sm">No logo</span>
                            <img id="logoPreview" src="" alt="" class="hidden w-full h-full object-contain">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800">
                            <p class="mt-1 text-xs text-gray-500">Max {{ $logoMaxKb ?? 512 }}KB. {{ $logoMimes ?? 'JPEG, PNG' }} only. Square logos recommended.</p>
                            @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea id="address" name="address" rows="2" class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('address') border-red-500 @enderror" placeholder="Full address">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">District</label>
                        <select id="district" name="district"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                            <option value="">Select district</option>
                            @foreach($districts ?? [] as $d)
                                <option value="{{ $d }}" {{ old('district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                        @error('district')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mandal" class="block text-sm font-medium text-gray-700 mb-2">Mandal</label>
                        <select id="mandal" name="mandal"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                            <option value="">Select mandal</option>
                        </select>
                        @error('mandal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                        <input type="text" value="Andhra Pradesh" readonly
                               class="block w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        <input type="hidden" name="state" value="Andhra Pradesh">
                        @error('state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">Pincode</label>
                        <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}" maxlength="10"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        @error('pincode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Person</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                            <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_name') border-red-500 @enderror">
                            @error('contact_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone <span class="text-red-500">*</span></label>
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_phone') border-red-500 @enderror"
                                   placeholder="10-digit mobile">
                            @error('contact_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                            <div class="flex-1">
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 @error('contact_email') border-red-500 @enderror"
                                       placeholder="e.g. contact@institute.com">
                                @error('contact_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <button type="button" id="sendOtpBtn" class="w-full sm:w-auto px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition-colors">
                                    Send OTP
                                </button>
                            </div>
                        </div>
                        <div id="otpSection" class="hidden">
                            <div class="flex-1">
                                <label for="email_otp" class="block text-sm font-medium text-gray-700 mb-2">Enter OTP (sent to your email)</label>
                                <input type="text" id="email_otp" maxlength="6" pattern="[0-9]*" inputmode="numeric"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                       placeholder="6-digit OTP">
                            </div>
                            <div>
                                <button type="button" id="verifyOtpBtn" class="w-full sm:w-auto px-4 py-3 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors">
                                    Verify
                                </button>
                            </div>
                        </div>
                        <p id="otpStatus" class="text-sm hidden"></p>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Registration
                </button>
                <p class="text-center text-sm text-gray-500">
                    Already have an account? <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:text-amber-700">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const mandalsByDistrict = @json($mandalsByDistrict ?? []);
    const districtEl = document.getElementById('district');
    const mandalEl = document.getElementById('mandal');
    const oldMandal = @json(old('mandal'));

    function updateMandals() {
        const district = districtEl.value;
        mandalEl.innerHTML = '<option value="">Select mandal</option>';
        if (!district || !mandalsByDistrict[district]) return;
        mandalsByDistrict[district].forEach(function(m) {
            const opt = document.createElement('option');
            opt.value = m;
            opt.textContent = m;
            if (oldMandal && oldMandal === m) opt.selected = true;
            mandalEl.appendChild(opt);
        });
    }

    districtEl.addEventListener('change', updateMandals);
    if (districtEl.value) updateMandals();

    // Logo preview
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    const logoPreviewWrap = document.getElementById('logoPreviewWrap');
    logoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('hidden');
                logoPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            logoPreview.src = '';
            logoPreview.classList.add('hidden');
            logoPlaceholder.classList.remove('hidden');
        }
    });

    // OTP send/verify
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const contactEmail = document.getElementById('contact_email');
    const emailOtp = document.getElementById('email_otp');
    const otpSection = document.getElementById('otpSection');
    const otpStatus = document.getElementById('otpStatus');

    function showOtpStatus(msg, isError) {
        otpStatus.textContent = msg;
        otpStatus.className = 'text-sm ' + (isError ? 'text-red-600' : 'text-green-600');
        otpStatus.classList.remove('hidden');
    }

    sendOtpBtn.addEventListener('click', async function() {
        const email = contactEmail.value?.trim();
        if (!email) {
            showOtpStatus('Enter your email first.', true);
            return;
        }
        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Sending…';
        otpStatus.classList.add('hidden');
        try {
            const res = await fetch('{{ route("partner.send-otp") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ email: email })
            });
            const data = await res.json();
            if (data.success) {
                showOtpStatus(data.message, false);
                otpSection.classList.remove('hidden');
                otpSection.classList.add('flex', 'flex-col', 'sm:flex-row', 'gap-2', 'sm:items-end');
                emailOtp.value = '';
                emailOtp.focus();
            } else {
                showOtpStatus(data.message || 'Failed to send OTP.', true);
            }
        } catch (e) {
            showOtpStatus('Network error. Please try again.', true);
        }
        sendOtpBtn.disabled = false;
        sendOtpBtn.textContent = 'Send OTP';
    });

    verifyOtpBtn.addEventListener('click', async function() {
        const email = contactEmail.value?.trim();
        const otp = emailOtp.value?.trim();
        if (!email || !otp) {
            showOtpStatus('Enter email and OTP.', true);
            return;
        }
        if (otp.length !== 6) {
            showOtpStatus('OTP must be 6 digits.', true);
            return;
        }
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying…';
        otpStatus.classList.add('hidden');
        try {
            const res = await fetch('{{ route("partner.verify-otp") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ email: email, otp: otp })
            });
            const data = await res.json();
            if (data.success) {
                showOtpStatus('✓ ' + data.message, false);
                otpSection.classList.add('hidden');
                sendOtpBtn.textContent = 'Verified';
                sendOtpBtn.disabled = true;
                sendOtpBtn.classList.remove('bg-amber-500', 'hover:bg-amber-600');
                sendOtpBtn.classList.add('bg-green-600', 'cursor-not-allowed');
            } else {
                showOtpStatus(data.message || 'Invalid OTP.', true);
            }
        } catch (e) {
            showOtpStatus('Network error. Please try again.', true);
        }
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.textContent = 'Verify';
    });
})();
</script>
@endsection
