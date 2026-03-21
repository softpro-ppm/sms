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

        .certificate {
            display: block;
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

        .content {
            position: relative;
            z-index: 3;
            padding: 14mm 16mm 10mm 16mm;
        }

        /* Table layout: fits all content on single page (DomPDF strips page 2) */
        .cert-layout-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .cert-layout-table td { padding: 0; vertical-align: top; }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #c4a574;
        }
        .header-table td { vertical-align: middle; padding: 0; }
        .header-table .col-logo { text-align: left; width: 28mm; padding-right: 6mm; }
        .header-table .col-institute { text-align: center; }
        .header-table .col-photo { text-align: right; width: 28mm; padding-left: 6mm; }
        .header-logo { height: 22mm; width: auto; vertical-align: middle; }
        .institute-name {
            font-size: 16pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .institute-tagline { font-size: 9pt; color: #6b5344; letter-spacing: 2px; }
        .institute-website { font-size: 9pt; color: #8b7355; }

        /* BODY ROW: height must fit title+paragraph+QR+signatures+footer (DomPDF clips overflow) */
        .body-cell {
            height: 155mm;
            vertical-align: top;
            padding: 0;
        }
        .body-inner {
            text-align: center;
            padding-top: 4mm;
        }

        .cert-title {
            font-size: 28pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 0 0 2mm 0;
        }
        .cert-subtitle {
            font-size: 9pt;
            color: #8b7355;
            letter-spacing: 6px;
            margin-bottom: 6mm;
        }
        .certify-line {
            font-size: 11pt;
            color: #4a3728;
            font-style: italic;
            margin: 0 0 2mm 0;
        }
        .recipient-name {
            font-size: 22pt;
            font-weight: 700;
            color: #2c1810;
            margin: 0 0 1mm 0;
            padding-bottom: 1mm;
            border-bottom: 2px solid #8b7355;
            display: inline-block;
        }
        .parent-line { font-size: 10pt; color: #5c4a3a; margin: 0 0 4mm 0; }
        .course-line {
            font-size: 11pt;
            color: #4a3728;
            line-height: 1.35;
            max-width: 150mm;
            margin: 0 auto 3mm auto;
        }
        .course-name { font-weight: 700; color: #2c1810; font-size: 13pt; }
        .course-dates {
            font-weight: 700;
            color: #2c1810;
            font-size: 12pt;
            font-family: 'DejaVu Sans', Arial, sans-serif;
        }
        .date-grade-line { font-size: 10pt; color: #5c4a3a; margin: 0 0 10mm 0; }

        /* QR BLOCK - center aligned for DomPDF */
        .qr-block {
            display: block;
            width: 100%;
            margin: 0 auto 18mm auto;
            text-align: center;
        }
        .qr-box {
            width: 14mm;
            height: 14mm;
            margin: 0 auto;
        }
        .qr-box img { width: 14mm; height: 14mm; }
        .qr-scan-text {
            font-size: 6pt;
            color: #6b5344;
            margin-top: 2mm;
            letter-spacing: 0.5px;
        }

        /* SIGNATURES - explicit column widths for DomPDF */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12mm;
            table-layout: fixed;
        }
        .signatures-table td { vertical-align: bottom; text-align: center; padding: 0 4mm; }
        .sig-td-left { width: 35%; text-align: center; }
        .sig-td-center { width: 30%; }
        .sig-td-right { width: 35%; text-align: center; }
        .signature-block { text-align: center; }
        .signature-line {
            width: 36mm;
            border-bottom: 1px solid #4a3728;
            margin: 0 auto 2mm auto;
        }
        .signature-label { font-size: 9pt; font-weight: 600; color: #2c1810; }
        .signature-org { font-size: 8pt; color: #6b5344; }

        /* FOOTER - inside body so it always renders */
        .footer-block {
            margin-top: 0;
            padding-top: 4mm;
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
        .cert-footer-row { table-layout: fixed; width: 100%; }
        .cert-footer-row td { padding: 2mm 4mm; letter-spacing: 0.3px; }
        .cert-footer-row .ft-left { width: 33%; text-align: left; }
        .cert-footer-row .ft-center { width: 34%; text-align: center; font-size: 8pt; color: #8b7355; }
        .cert-footer-row .ft-right { width: 33%; text-align: right; }

        .photo-cell { text-align: center; }
        .photo-box {
            width: 22mm;
            height: 28mm;
            border: 2px solid #8b7355;
            background: #ffffff;
            overflow: hidden;
            margin: 0 auto;
        }
        .photo-box img { width: 22mm; height: 28mm; object-fit: cover; }
        .enrollment-badge { margin-top: 1mm; font-size: 7pt; color: #6b5344; font-weight: 600; }

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
            <table class="cert-layout-table" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table class="header-table" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="col-logo">
                                    <img src="{{ $logoPath }}" alt="SoftPro" class="header-logo">
                                </td>
                                <td class="col-institute">
                                    <div class="institute-name">Softpro Skill Solutions</div>
                                    <div class="institute-tagline">Skill Development &amp; Training Institute</div>
                                    <div class="institute-website">www.softpro.co.in</div>
                                </td>
                                <td class="col-photo">
                                    @if($studentPhotoUrl)
                                    <div class="photo-cell">
                                        <div class="photo-box">
                                            <img src="{{ $studentPhotoPath }}" alt="">
                                        </div>
                                        <div class="enrollment-badge">Enrol. {{ $enrollmentNumber }}</div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="body-cell">
                        <div class="body-inner">
                            <h1 class="cert-title">{{ $certificateTitle }}</h1>
                            <p class="cert-subtitle">◆ Certificate of Achievement ◆</p>
                            <p class="certify-line">This is to certify that</p>
                            <p class="recipient-name">{{ $student->full_name }}</p>
                            <p class="parent-line">{{ $parentLabel }} {{ $parentName ?: '—' }}</p>
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
                            <p class="date-grade-line">Issue Date: {{ $issueDate }}</p>

                            <div class="qr-block">
                                @if($qrUrl)
                                <div class="qr-box">
                                    <img src="{{ $qrUrl }}" alt="Verify">
                                </div>
                                <div class="qr-scan-text">Scan to Verify Certificate</div>
                                @endif
                            </div>

                            <table class="signatures-table" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="sig-td-left">
                                        <div class="signature-block">
                                            <div class="signature-line"></div>
                                            <div class="signature-label">Authorized Signatory</div>
                                            <div class="signature-org">(Seal)</div>
                                        </div>
                                    </td>
                                    <td class="sig-td-center">&nbsp;</td>
                                    <td class="sig-td-right">
                                        <div class="signature-block">
                                            <div class="signature-line"></div>
                                            <div class="signature-label">Director</div>
                                            <div class="signature-org">Softpro Skill Solutions</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="footer-block">
                                <table class="cert-footer-row" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="ft-left">Enrol. <strong>{{ $enrollmentNumber }}</strong></td>
                                        <td class="ft-center">{{ $isoText }}</td>
                                        <td class="ft-right">Cert No. <strong>{{ $certificate->certificate_number }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
