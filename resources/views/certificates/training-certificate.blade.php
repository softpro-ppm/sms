<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'DejaVu Serif', Georgia, 'Times New Roman', serif;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            page-break-inside: avoid;
        }

        .certificate::before {
            content: '';
            position: absolute;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #8b7355;
            pointer-events: none;
            z-index: 1;
        }

        .certificate::after {
            content: '';
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #c4a574;
            pointer-events: none;
            z-index: 1;
        }

        .corner {
            position: absolute;
            width: 24mm;
            height: 24mm;
            border-color: #8b7355;
            border-style: solid;
            border-width: 0;
            z-index: 2;
        }
        .corner-tl { top: 9mm; left: 9mm; border-top-width: 2px; border-left-width: 2px; }
        .corner-tr { top: 9mm; right: 9mm; border-top-width: 2px; border-right-width: 2px; }
        .corner-bl { bottom: 9mm; left: 9mm; border-bottom-width: 2px; border-left-width: 2px; }
        .corner-br { bottom: 9mm; right: 9mm; border-bottom-width: 2px; border-right-width: 2px; }

        /* Content area - all children use absolute positioning */
        .content {
            position: absolute;
            top: 14mm;
            left: 16mm;
            right: 16mm;
            bottom: 10mm;
            z-index: 3;
        }

        /* ===== FIXED ABSOLUTE POSITIONS (mm) ===== */

        /* 1) HEADER: top 0, height 24mm */
        .section-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 24mm;
            border-bottom: 1px solid #c4a574;
        }
        .header-logo {
            position: absolute;
            top: 1mm;
            left: 0;
            height: 22mm;
            width: auto;
        }
        .header-institute {
            position: absolute;
            top: 2mm;
            left: 30mm;
            right: 30mm;
            text-align: center;
        }
        .header-photo {
            position: absolute;
            top: 0;
            right: 0;
            text-align: right;
        }
        .institute-name {
            font-size: 16pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .institute-tagline { font-size: 9pt; color: #6b5344; letter-spacing: 2px; }
        .institute-website { font-size: 9pt; color: #8b7355; }
        .photo-box {
            width: 22mm;
            height: 28mm;
            border: 2px solid #8b7355;
            background: #ffffff;
            overflow: hidden;
        }
        .photo-box img { width: 22mm; height: 28mm; object-fit: cover; }
        .enrollment-badge { font-size: 7pt; color: #6b5344; font-weight: 600; }

        /* 2) TITLE: top 28mm, height 20mm */
        .section-title {
            position: absolute;
            top: 28mm;
            left: 0;
            right: 0;
            height: 20mm;
            text-align: center;
        }
        .cert-title {
            font-size: 28pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .cert-subtitle {
            font-size: 9pt;
            color: #8b7355;
            letter-spacing: 6px;
        }

        /* 3) CERTIFY: top 50mm, height 6mm */
        .section-certify {
            position: absolute;
            top: 50mm;
            left: 0;
            right: 0;
            height: 6mm;
            text-align: center;
        }
        .certify-line {
            font-size: 11pt;
            color: #4a3728;
            font-style: italic;
        }

        /* 4) NAME: top 57mm, height 14mm */
        .section-name {
            position: absolute;
            top: 57mm;
            left: 0;
            right: 0;
            height: 14mm;
            text-align: center;
        }
        .recipient-name {
            font-size: 22pt;
            font-weight: 700;
            color: #2c1810;
            border-bottom: 2px solid #8b7355;
            display: inline-block;
        }

        /* 5) FATHER: top 72mm, height 6mm */
        .section-father {
            position: absolute;
            top: 72mm;
            left: 0;
            right: 0;
            height: 6mm;
            text-align: center;
        }
        .parent-line { font-size: 10pt; color: #5c4a3a; }

        /* 6) BODY TEXT BLOCK: paragraph + issue date centered as one unit */
        .body-text-wrapper {
            position: absolute;
            top: 80mm;
            left: 50%;
            transform: translateX(-50%);
            width: 150mm;
            text-align: center;
        }
        .section-paragraph {
            height: 28mm;
        }
        .section-paragraph table {
            border-collapse: collapse;
        }
        .course-line {
            font-size: 11pt;
            color: #4a3728;
            line-height: 1.35;
        }
        .course-name { font-weight: 700; color: #2c1810; font-size: 13pt; }
        .course-dates {
            font-weight: 700;
            color: #2c1810;
            font-size: 12pt;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        /* 7) ISSUE DATE: inside body-text-wrapper */
        .section-issue {
            height: 8mm;
            text-align: center;
        }
        .date-grade-line { font-size: 10pt; color: #5c4a3a; }

        /* 8) QR: top 122mm - single centered block (QR + caption) */
        .qr-wrapper {
            position: absolute;
            top: 122mm;
            left: 50%;
            transform: translateX(-50%);
            width: 20mm;
            text-align: center;
        }
        .qr-wrapper .qr-box {
            width: 18mm;
            height: 18mm;
            text-align: center;
        }
        .qr-wrapper .qr-box img {
            width: 18mm;
            height: 18mm;
        }
        .qr-wrapper .qr-scan-text {
            font-size: 6pt;
            color: #6b5344;
            letter-spacing: 0.5px;
            padding-top: 2.5mm;
            width: 18mm;
            display: inline-block;
            text-align: center;
            white-space: nowrap;
        }

        /* 9) SIGNATURES: top 148mm, height 20mm - same baseline */
        .section-signatures {
            position: absolute;
            top: 148mm;
            left: 0;
            right: 0;
            height: 20mm;
        }
        .signature-left {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-right {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-sig-line {
            display: block;
            text-align: center;
            margin-bottom: 0.5mm;
        }
        .signature-sig-line .signature-line-bar {
            width: 36mm;
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .signature-sig-line .signature-line-bar td {
            width: 36mm;
            border-bottom: 1px solid #4a3728;
            height: 3mm;
            line-height: 0;
            font-size: 0;
        }
        .signature-sig-line .signature-img {
            display: block;
            height: 25.5mm;
            width: auto;
            margin: 0 auto -7mm auto;
        }
        .signature-line-wrap {
            margin-bottom: 0.5mm;
            text-align: center;
        }
        .signature-line-wrap .signature-line-bar {
            width: 36mm;
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .signature-line-wrap .signature-line-bar td {
            width: 36mm;
            border-bottom: 1px solid #4a3728;
            height: 3mm;
            line-height: 0;
            font-size: 0;
        }
        .signature-line {
            width: 36mm;
            border-bottom: 1px solid #4a3728;
            display: inline-block;
        }
        .signature-img {
            height: 24mm;
            width: auto;
            display: inline-block;
        }
        .signature-label { font-size: 9pt; font-weight: 600; color: #2c1810; }
        .signature-org { font-size: 8pt; color: #6b5344; }

        /* 10) FOOTER: absolute block near bottom, relative to certificate */
        .section-footer {
            position: absolute;
            left: 20mm;
            right: 20mm;
            bottom: 12mm;
            height: 10mm;
            font-size: 9pt;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-weight: 500;
            color: #2c1810;
            z-index: 4;
        }
        .footer-left {
            position: absolute;
            left: 0;
            top: 2mm;
        }
        .footer-center {
            position: absolute;
            left: 33%;
            right: 33%;
            top: 2mm;
            font-size: 8pt;
            color: #8b7355;
            text-align: center;
        }
        .footer-right {
            position: absolute;
            right: 0;
            top: 2mm;
            text-align: right;
        }

        @media print {
            html, body { margin: 0 !important; padding: 0 !important; background: #ffffff !important; }
            .certificate { box-shadow: none; background: #ffffff !important; }
            .photo-box { background: #ffffff !important; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="content">
            <!-- 1) HEADER: top 0, 24mm -->
            <div class="section-header">
                <img src="{{ $logoPath }}" alt="SoftPro" class="header-logo">
                <div class="header-institute">
                    <div class="institute-name">Softpro Skill Solutions</div>
                    <div class="institute-tagline">Skill Development &amp; Training Institute</div>
                    <div class="institute-website">www.softpro.co.in</div>
                </div>
                @if($studentPhotoUrl)
                <div class="header-photo">
                    <div class="photo-box">
                        <img src="{{ $studentPhotoPath }}" alt="">
                    </div>
                    <div class="enrollment-badge">Enrol. {{ $enrollmentNumber }}</div>
                </div>
                @endif
            </div>

            <!-- 2) TITLE: top 28mm -->
            <div class="section-title">
                <h1 class="cert-title">{{ $certificateTitle }}</h1>
                <p class="cert-subtitle">◆ Certificate of Achievement ◆</p>
            </div>

            <!-- 3) CERTIFY: top 50mm -->
            <div class="section-certify">
                <p class="certify-line">This is to certify that</p>
            </div>

            <!-- 4) NAME: top 57mm -->
            <div class="section-name">
                <p class="recipient-name">{{ $student->full_name }}</p>
            </div>

            <!-- 5) FATHER: top 72mm -->
            <div class="section-father">
                <p class="parent-line">{{ $parentLabel }} {{ $parentName ?: '—' }}</p>
            </div>

            <!-- 6) BODY TEXT BLOCK: paragraph + issue date centered together -->
            <div class="body-text-wrapper">
                <div class="section-paragraph">
                    <table style="width: 100%" cellpadding="0" cellspacing="0"><tr><td align="center">
                    <p class="course-line">
                        has successfully completed the course <span class="course-name">{{ $course->name }}</span>
                        @if($batch)
                        (Batch: {{ $batch->batch_name }})
                        @endif
                        conducted by Softpro Skill Solutions during the period <span class="course-dates">{{ $startDate }} – {{ $endDate }}</span>
                        @if($grade && $grade !== 'N/A')
                        and has secured grade <strong>{{ $grade }}</strong>
                        @endif
                        based on overall performance, attendance and assessment.
                    </p>
                    </td></tr></table>
                </div>
                <div class="section-issue">
                    <p class="date-grade-line">Issue Date: {{ $issueDate }}</p>
                </div>
            </div>

            <!-- 8) QR: top 122mm - centered wrapper -->
            @if($qrUrl)
            <div class="qr-wrapper">
                <div class="qr-box">
                    <img src="{{ $qrUrl }}" alt="Verify">
                </div>
                <div class="qr-scan-text">Scan to Verify</div>
            </div>
            @endif

            <!-- 9) SIGNATURES: top 148mm -->
            <div class="section-signatures">
                <div class="signature-left">
                    <div class="signature-line-wrap">
                        <table class="signature-line-bar" cellpadding="0" cellspacing="0"><tr><td>&nbsp;</td></tr></table>
                    </div>
                    <div class="signature-label">Authorized Signatory</div>
                    <div class="signature-org">(Seal)</div>
                </div>
                <div class="signature-right">
                    <div class="signature-sig-line">
                        @if(isset($directorSignaturePath) && $directorSignaturePath)
                        <img src="{{ $directorSignaturePath }}" alt="Director Signature" class="signature-img">
                        @endif
                        <table class="signature-line-bar" cellpadding="0" cellspacing="0"><tr><td>&nbsp;</td></tr></table>
                    </div>
                    <div class="signature-label">Director</div>
                    <div class="signature-org">Softpro Skill Solutions</div>
                </div>
            </div>
        </div>

        <!-- 10) FOOTER: absolute, relative to certificate -->
        <div class="section-footer">
            <span class="footer-left">Enrol. <strong>{{ $enrollmentNumber }}</strong></span>
            <span class="footer-center">{{ $isoText }}</span>
            <span class="footer-right">Cert No. <strong>{{ $certificate->certificate_number }}</strong></span>
        </div>
    </div>
</body>
</html>
