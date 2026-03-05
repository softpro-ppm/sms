<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student ID - {{ $student->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: 85.6mm 54mm; margin: 0; }
        html, body { width: 85.6mm; height: 54mm; margin: 0; padding: 0; overflow: hidden; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9pt; line-height: 1.2; color: #0F172A; }
        .id-card {
            position: relative;
            width: 85.6mm;
            height: 54mm;
            overflow: hidden;
            background: #FFF;
            page-break-inside: avoid;
        }
        .safe-area {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 3mm;
        }
        .hdr {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 12mm;
            background: #E85D04;
        }
        .hdr-tbl { width: 100%; border-collapse: collapse; height: 100%; }
        .hdr-tbl td { vertical-align: middle; }
        .hdr-left { padding-left: 2.5mm; }
        .hdr-title { font-size: 10pt; font-weight: bold; color: #FFF; }
        .hdr-sub { font-size: 8pt; font-weight: bold; color: #FFF; margin-top: 0.3mm; }
        .logo-right { text-align: right; padding-right: 2.5mm; }
        .logo-box { background: #FFF; padding: 0.5mm 1mm; display: inline-block; }
        .logo-box img { max-height: 9mm; max-width: 12mm; }
        .card-body {
            position: absolute;
            top: 14mm;
            bottom: 14mm;
            left: 0;
            right: 0;
            padding: 0 1mm;
            background: #FFF;
        }
        .body-tbl { width: 100%; border-collapse: collapse; }
        .body-tbl td { vertical-align: top; }
        .pic-td { width: 24mm; padding-right: 2mm; }
        .pic-box {
            width: 21mm;
            height: 27mm;
            border: 2px solid #E85D04;
            background: #FFF;
            overflow: hidden;
        }
        .pic-box img {
            height: 27mm;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        .name { font-size: 11pt; font-weight: bold; color: #0F172A; margin-bottom: 0.3mm; }
        .position { font-size: 9pt; color: #64748B; margin-bottom: 1mm; }
        .lbl { font-size: 8pt; color: #64748B; }
        .val { font-size: 9pt; font-weight: 600; color: #0F172A; }
        .row { margin-bottom: 0.3mm; }
        .bottom-row { margin-top: 2mm; }
        .sign-wrap {
            position: absolute;
            left: 6mm;
            bottom: 2mm;
        }
        .sig-block { text-align: left; }
        .sig-block img { height: 6mm; display: block; margin: 0 0 0 0; }
        .sig-line { border-top: 1px solid #334155; width: 22mm; margin: 0; }
        .sig-text { font-size: 8pt; color: #64748B; margin-top: 0.2mm; }
        .qr-wrap {
            position: absolute;
            right: 6mm;
            bottom: 2mm;
            text-align: center;
        }
        .qr-wrap svg,
        .qr-wrap img {
            width: 16mm !important;
            height: 16mm !important;
            display:block;
            margin: 0 auto;
        }
        .qr-caption {
            font-size: 6px;
            color: #64748B;
            margin-bottom: 1mm;
        }
    </style>
</head>
<body>
    @php
        $photoDoc = $student->documents()->where('document_type', 'photo')->first();
        $photoPath = null;
        if ($photoDoc && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoDoc->file_path)) {
            $photoPath = storage_path('app/public/' . $photoDoc->file_path);
        }
        $logoPath = file_exists(public_path('images/logo/Logo_png.png')) ? public_path('images/logo/Logo_png.png') : null;
        $activeEnrollment = $student->enrollments()->where('status', 'active')->with('batch.course')->first();
        $enrollmentNumber = $activeEnrollment?->enrollment_number ?? ('ID' . $student->id);
        $courseName = $activeEnrollment?->batch?->course?->name ?? 'Student';
        $batch = $activeEnrollment?->batch;
        $startDate = $batch?->start_date;
        $issueDateFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->timezone('Asia/Kolkata')->format('d-m-Y') : null;
        $expireDateFormatted = $startDate ? \Carbon\Carbon::parse($startDate)->timezone('Asia/Kolkata')->addYear()->format('d-m-Y') : null;
        $baseUrl = rtrim(config('app.url', 'https://sms.softpromis.com'), '/');
        $verifyUrl = $baseUrl . '/verify/' . $enrollmentNumber;
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->generate($verifyUrl);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        $signaturePath = \App\Services\SignatureImageService::getTransparentSignaturePath();
    @endphp

    <div class="id-card">
        <div class="safe-area">
        <div class="hdr">
            <table class="hdr-tbl" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="hdr-left">
                        <div class="hdr-title">Softpro Skill Solutions</div>
                        <div class="hdr-sub">Student ID Card</div>
                    </td>
                    <td class="logo-right" style="width:25mm;">
                        @if($logoPath)
                        <div class="logo-box"><img src="{{ $logoPath }}" alt="Softpro"></div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-body">
            <table class="body-tbl" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="pic-td">
                        @if($photoPath && file_exists($photoPath))
                        <div class="pic-box">
                            <img src="{{ $photoPath }}" alt="">
                        </div>
                        @else
                        <table cellpadding="0" cellspacing="0" style="width:21mm;height:27mm;border:2px dashed #E85D04;background:#FFF5EB;"><tr><td align="center" valign="middle" style="font-size:4pt;color:#64748B;">Photo</td></tr></table>
                        @endif
                    </td>
                    <td>
                        <div class="name">{{ $student->full_name }}</div>
                        <div class="position">{{ $courseName }}</div>
                        <div class="row"><span class="lbl">ID No :</span> <span class="val">{{ $enrollmentNumber }}</span></div>
                        @if($issueDateFormatted)
                        <div class="row"><span class="lbl">Issue Date :</span> <span class="val">{{ $issueDateFormatted }}</span></div>
                        <div class="row"><span class="lbl">Expire Date :</span> <span class="val">{{ $expireDateFormatted }}</span></div>
                        @endif
                        <div class="row"><span class="lbl">Mobile :</span> <span class="val">{{ $student->whatsapp_number ?? $student->phone ?? '-' }}</span></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="sign-wrap">
            <div class="sig-block">
                @if($signaturePath)
                <img src="{{ $signaturePath }}" alt="" style="margin-bottom:0;">
                @endif
                <div class="sig-line" style="margin-top:0;"></div>
                <div class="sig-text">Authorised Signatory</div>
            </div>
        </div>

        <div class="qr-wrap">
            <div class="qr-caption">Scan to Verify</div>
            <img src="{{ $qrBase64 }}" alt="QR Verify">
        </div>
        </div>
    </div>
</body>
</html>
