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
            text-align: left;
        }

        /* A4 Landscape: 297mm x 210mm - strict block layout */
        .certificate {
            display: block;
            position: relative;
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            page-break-inside: avoid;
        }

        /* Outer border: 8mm inset from all sides */
        .certificate::before {
            content: '';
            position: absolute;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #8b7355;
            pointer-events: none;
            z-index: 1;
        }

        /* Inner border: 11mm inset from all sides */
        .certificate::after {
            content: '';
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #c4a574;
            pointer-events: none;
            z-index: 1;
        }

        /* Corner ornaments aligned to borders */
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

        /* Content area: 14mm top, 16mm sides; 18mm bottom for ~8-10mm gap to inner border */
        .content {
            position: relative;
            z-index: 3;
            padding: 14mm 16mm 18mm 16mm;
        }

        /* ========== BLOCK 1: HEADER (starts 14mm from content top, height 24mm) ========== */
        .header-block {
            height: 24mm;
            display: table;
            width: 100%;
            border-bottom: 1px solid #c4a574;
            margin-bottom: 0;
        }
        .header-block > div { display: table-cell; vertical-align: middle; }
        .header-col-logo {
            width: 28mm;
            text-align: left;
            padding-right: 4mm;
            vertical-align: middle;
        }
        .header-col-title {
            text-align: center;
            vertical-align: middle;
        }
        .header-col-photo {
            width: 28mm;
            text-align: right;
            padding-left: 4mm;
            vertical-align: middle;
        }
        .header-logo {
            height: 20mm;
            width: auto;
            max-height: 20mm;
            vertical-align: middle;
        }
        .institute-name {
            font-size: 16pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2mm;
            line-height: 1.2;
        }
        .institute-tagline {
            font-size: 9pt;
            color: #6b5344;
            letter-spacing: 2px;
            margin-top: 0.5mm;
        }
        .institute-website {
            font-size: 9pt;
            color: #8b7355;
            margin-top: 0.5mm;
        }

        /* ========== BLOCK 2: MAIN TITLE (18mm below header; +4mm for vertical balance) ========== */
        .title-block {
            margin-top: 18mm;
            height: 18mm;
            text-align: center;
        }
        .cert-title {
            font-size: 28pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
        }
        .cert-subtitle {
            font-size: 9pt;
            color: #8b7355;
            letter-spacing: 6px;
            margin-top: 4mm;
            margin-bottom: 0;
        }

        /* ========== BLOCK 3: CERTIFY LINE (5mm below title, height 6mm) ========== */
        .certify-block {
            margin-top: 5mm;
            height: 6mm;
            text-align: center;
        }
        .certify-line {
            font-size: 11pt;
            color: #4a3728;
            font-style: italic;
            margin: 0;
        }

        /* ========== BLOCK 4: STUDENT NAME (3mm below certify, height 12mm) ========== */
        .name-block {
            margin-top: 3mm;
            height: 12mm;
            text-align: center;
        }
        .recipient-name {
            font-size: 22pt;
            font-weight: 700;
            color: #2c1810;
            margin: 0;
            padding-bottom: 1mm;
            border-bottom: 2px solid #8b7355;
            display: inline-block;
        }

        /* ========== BLOCK 5: FATHER NAME (2mm below name underline, height 5mm) ========== */
        .father-block {
            margin-top: 2mm;
            height: 5mm;
            text-align: center;
        }
        .parent-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin: 0;
        }

        /* ========== BLOCK 6: BODY PARAGRAPH (6mm below father, max-width 150mm, ~22mm) ========== */
        .body-block {
            margin: 6mm auto 0 auto;
            text-align: center;
            max-width: 150mm;
            line-height: 1.35;
        }
        .course-line {
            font-size: 11pt;
            color: #4a3728;
            margin: 0;
        }
        .course-name {
            font-weight: 700;
            color: #2c1810;
            font-size: 13pt;
        }
        .course-dates {
            font-weight: 700;
            color: #2c1810;
            font-size: 12pt;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }

        /* ========== BLOCK 7: ISSUE DATE (4mm below body, height 6mm) ========== */
        .issue-block {
            margin-top: 4mm;
            height: 6mm;
            text-align: center;
        }
        .date-grade-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin: 0;
        }

        /* ========== BLOCK 8: QR (12mm below issue date; +4mm space) ========== */
        .qr-block {
            margin-top: 12mm;
            min-height: 18mm;
            height: 18mm;
            text-align: center;
        }
        .qr-box {
            width: 13mm;
            height: 13mm;
            margin: 0 auto;
        }
        .qr-box img {
            width: 13mm;
            height: 13mm;
        }
        .qr-scan-text {
            font-size: 6pt;
            color: #6b5344;
            margin-top: 2mm;
            letter-spacing: 0.5px;
        }

        /* ========== BLOCK 9: SIGNATURES (18mm below QR; symmetrical left/right) ========== */
        .signature-block-wrapper {
            margin-top: 18mm;
            margin-bottom: 0;
            min-height: 12mm;
            position: relative;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .signatures-table td { vertical-align: bottom; text-align: center; padding: 0; }
        .sig-col-left { width: 54mm; text-align: center; }   /* ends at 70mm from page left */
        .sig-col-center { width: 157mm; }                   /* spacer 70mm to 227mm */
        .sig-col-right { width: 54mm; text-align: center; } /* starts at 227mm from page left */
        .signature-block {
            display: inline-block;
            text-align: center;
        }
        .signature-line {
            width: 32mm;
            border-bottom: 1px solid #4a3728;
            margin: 0 auto 2mm;
        }
        .signature-label {
            font-size: 9pt;
            font-weight: 600;
            color: #2c1810;
        }
        .signature-org {
            font-size: 8pt;
            color: #6b5344;
            margin-top: 0.5px;
        }

        /* ========== BLOCK 10: FOOTER (12mm below signatures; ~8-10mm from inner bottom border) ========== */
        .footer-block {
            margin-top: 12mm;
            padding-top: 3mm;
            padding-bottom: 4mm;
            border-top: 1px solid #c4a574;
        }
        .cert-footer-row {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-weight: 500;
            color: #2c1810;
        }
        .cert-footer-row td { padding: 0; letter-spacing: 0.3px; }
        .cert-footer-row td:first-child { text-align: left; width: 33%; }
        .cert-footer-row td:nth-child(2) { text-align: center; font-size: 8pt; color: #8b7355; }
        .cert-footer-row td:last-child { text-align: right; width: 33%; }

        /* Photo in header */
        .photo-cell { text-align: center; }
        .photo-box {
            width: 22mm;
            height: 28mm;
            border: 2px solid #8b7355;
            background: #ffffff;
            overflow: hidden;
            margin: 0 auto;
        }
        .photo-box img {
            width: 22mm;
            height: 28mm;
            object-fit: cover;
        }
        .enrollment-badge {
            margin-top: 1mm;
            font-size: 7pt;
            color: #6b5344;
            font-weight: 600;
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
            <!-- 1) HEADER BLOCK: 14mm from content top, height 24mm -->
            <div class="header-block">
                <div class="header-col-logo">
                    <img src="{{ $logoPath }}" alt="SoftPro" class="header-logo">
                </div>
                <div class="header-col-title">
                    <div class="institute-name">Softpro Skill Solutions</div>
                    <div class="institute-tagline">Skill Development &amp; Training Institute</div>
                    <div class="institute-website">www.softpro.co.in</div>
                </div>
                <div class="header-col-photo">
                    @if($studentPhotoUrl)
                    <div class="photo-cell">
                        <div class="photo-box">
                            <img src="{{ $studentPhotoPath }}" alt="">
                        </div>
                        <div class="enrollment-badge">Enrol. {{ $enrollmentNumber }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 2) MAIN TITLE BLOCK: 12mm below header -->
            <div class="title-block">
                <h1 class="cert-title">{{ $certificateTitle }}</h1>
                <p class="cert-subtitle">◆ Certificate of Achievement ◆</p>
            </div>

            <!-- 3) CERTIFY LINE -->
            <div class="certify-block">
                <p class="certify-line">This is to certify that</p>
            </div>

            <!-- 4) STUDENT NAME -->
            <div class="name-block">
                <p class="recipient-name">{{ $student->full_name }}</p>
            </div>

            <!-- 5) FATHER NAME -->
            <div class="father-block">
                <p class="parent-line">{{ $parentLabel }} {{ $parentName ?: '—' }}</p>
            </div>

            <!-- 6) BODY PARAGRAPH -->
            <div class="body-block">
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
            </div>

            <!-- 7) ISSUE DATE -->
            <div class="issue-block">
                <p class="date-grade-line">Issue Date: {{ $issueDate }}</p>
            </div>

            <!-- 8) QR BLOCK -->
            <div class="qr-block">
                @if($qrUrl)
                <div class="qr-box">
                    <img src="{{ $qrUrl }}" alt="Verify">
                </div>
                <div class="qr-scan-text">Scan to Verify Certificate</div>
                @endif
            </div>

            <!-- 9) SIGNATURE BLOCK -->
            <div class="signature-block-wrapper">
                <table class="signatures-table" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="sig-col-left">
                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Authorized Signatory</div>
                                <div class="signature-org">(Seal)</div>
                            </div>
                        </td>
                        <td class="sig-col-center"></td>
                        <td class="sig-col-right">
                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Director</div>
                                <div class="signature-org">Softpro Skill Solutions</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- 10) FOOTER: at 182mm from page top -->
            <div class="footer-block">
                <table class="cert-footer-row" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>Enrol. <strong>{{ $enrollmentNumber }}</strong></td>
                        <td>{{ $isoText }}</td>
                        <td>Cert No. <strong>{{ $certificate->certificate_number }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
