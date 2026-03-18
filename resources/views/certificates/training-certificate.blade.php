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
            padding: 8px;
            text-align: center;
        }

        /* A4 landscape: 297mm x 210mm */
        .certificate {
            display: inline-block;
            position: relative;
            text-align: left;
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 12px 48px rgba(0,0,0,0.15);
            page-break-inside: avoid;
        }

        /* Outer ornamental border */
        .certificate::before {
            content: '';
            position: absolute;
            top: 10mm; left: 10mm; right: 10mm; bottom: 10mm;
            border: 3px solid #8b7355;
            pointer-events: none;
            z-index: 1;
        }

        /* Inner ornamental border */
        .certificate::after {
            content: '';
            position: absolute;
            top: 13mm; left: 13mm; right: 13mm; bottom: 13mm;
            border: 1px solid #c4a574;
            pointer-events: none;
            z-index: 1;
        }

        /* Corner ornaments */
        .corner {
            position: absolute;
            width: 24mm;
            height: 24mm;
            border-color: #8b7355;
            border-style: solid;
            border-width: 0;
            z-index: 2;
        }
        .corner-tl { top: 14mm; left: 14mm; border-top-width: 2px; border-left-width: 2px; }
        .corner-tr { top: 14mm; right: 14mm; border-top-width: 2px; border-right-width: 2px; }
        .corner-bl { bottom: 14mm; left: 14mm; border-bottom-width: 2px; border-left-width: 2px; }
        .corner-br { bottom: 14mm; right: 14mm; border-bottom-width: 2px; border-right-width: 2px; }

        /* Content: fixed height 180mm (inside inner border) */
        .content {
            position: relative;
            z-index: 3;
            height: 180mm;
            padding: 0 18mm;
        }

        /* Top section: flows from top */
        .top-section {
            padding-top: 5mm;
        }

        /* Bottom section: anchored ~20mm above bottom border */
        .bottom-section {
            position: absolute;
            bottom: 6mm;
            left: 0;
            right: 0;
        }

        /* Header: Logo + Institute + Photo */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            padding-bottom: 3mm;
            border-bottom: 1px solid #c4a574;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .header-table .col-logo {
            text-align: right;
            padding-right: 8mm;
            width: 1%;
        }
        .header-table .col-institute {
            text-align: center;
        }
        .header-table .col-photo {
            text-align: left;
            padding-left: 8mm;
            width: 1%;
        }

        .header-logo {
            height: 20mm;
            width: auto;
        }

        .institute-name {
            font-size: 16pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .institute-tagline {
            font-size: 9pt;
            color: #6b5344;
            letter-spacing: 2px;
            margin-top: 1px;
        }

        .institute-website {
            font-size: 9pt;
            color: #8b7355;
            margin-top: 2px;
        }

        /* Main title */
        .cert-title {
            text-align: center;
            font-size: 26pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 4px;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 9pt;
            color: #8b7355;
            letter-spacing: 6px;
            margin-bottom: 3mm;
        }

        /* Body */
        .cert-body {
            text-align: center;
            padding: 2mm 0 2mm 0;
        }

        .certify-line {
            font-size: 11pt;
            color: #4a3728;
            margin-top: 2mm;
            margin-bottom: 1mm;
            font-style: italic;
        }

        .recipient-name {
            font-size: 22pt;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 1mm;
            padding-bottom: 2mm;
            border-bottom: 2px solid #8b7355;
            display: inline-block;
        }

        .parent-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin-bottom: 2mm;
        }

        .course-line {
            font-size: 11pt;
            color: #4a3728;
            line-height: 1.5;
            max-width: 220mm;
            margin-left: auto;
            margin-right: auto;
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

        .date-grade-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin-top: 2mm;
            margin-bottom: 0;
        }


        /* Signatures - table for DomPDF */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
        }
        .signatures-table td {
            vertical-align: bottom;
            text-align: center;
            width: 33%;
        }

        .signature-block {
            text-align: center;
            width: 50mm;
            margin: 0 auto;
        }

        .signature-center {
            text-align: center;
        }

        .signature-center .qr-box {
            width: 18mm;
            height: 18mm;
            margin: 0 auto;
        }

        .signature-center .qr-box img {
            width: 18mm;
            height: 18mm;
        }

        .qr-scan-text {
            font-size: 6pt;
            color: #6b5344;
            margin-top: 1mm;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .signature-line {
            width: 45mm;
            height: 8mm;
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
            margin-top: 1px;
        }

        /* Footer - just above bottom border */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px solid #c4a574;
        }
        .footer-table td {
            padding: 1mm 0;
            font-size: 9pt;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-weight: 500;
            color: #2c1810;
            letter-spacing: 0.3px;
        }
        .footer-table td:first-child { text-align: left; width: 33%; }
        .footer-table td:nth-child(2) { text-align: center; font-size: 9pt; color: #8b7355; }
        .footer-table td:last-child { text-align: right; width: 33%; }

        /* Photo in header */
        .photo-cell {
            text-align: center;
        }
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
            text-align: center;
        }

        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            .certificate {
                box-shadow: none;
                background: #ffffff !important;
            }
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
            <div class="top-section">
                <table class="header-table">
                            <tr>
                                <td class="col-logo"><img src="{{ $logoPath }}" alt="SoftPro" class="header-logo"></td>
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

                        <h1 class="cert-title">{{ $certificateTitle }}</h1>
                        <p class="cert-subtitle">◆ Certificate of Achievement ◆</p>

                        <div class="cert-body">
                            <p class="certify-line">This is to certify that</p>
                            <p class="recipient-name">{{ $student->full_name }}</p>
                            <p class="parent-line">{{ $parentLabel }} {{ $parentName ?: '_______________' }}</p>
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
                        </div>
            </div>

            <div class="bottom-section">
                            <table class="signatures-table">
                                <tr>
                                    <td>
                                        <div class="signature-block">
                                            <div class="signature-line"></div>
                                            <div class="signature-label">Authorized Signatory</div>
                                            <div class="signature-org">(Seal)</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="signature-center">
                                            @if($qrUrl)
                                            <div class="qr-box">
                                                <img src="{{ $qrUrl }}" alt="Verify">
                                            </div>
                                            <div class="qr-scan-text">Scan to Verify Certificate</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="signature-block">
                                            <div class="signature-line"></div>
                                            <div class="signature-label">Director</div>
                                            <div class="signature-org">Softpro Skill Solutions</div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table class="footer-table">
                                <tr>
                                    <td>Enrol. <strong>{{ $enrollmentNumber }}</strong></td>
                                    <td>{{ $isoText }}</td>
                                    <td>Certificate No. <strong>{{ $certificate->certificate_number }}</strong></td>
                                </tr>
                            </table>
            </div>
        </div>
    </div>
</body>
</html>
