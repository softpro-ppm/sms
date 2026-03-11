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

        body {
            font-family: 'DejaVu Serif', Georgia, 'Times New Roman', serif;
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .certificate {
            position: relative;
            width: 297mm;
            height: 210mm;
            background: #faf8f5;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 12px 48px rgba(0,0,0,0.15);
        }

        /* Outer ornamental border */
        .certificate::before {
            content: '';
            position: absolute;
            top: 12mm; left: 12mm; right: 12mm; bottom: 12mm;
            border: 3px solid #8b7355;
            pointer-events: none;
            z-index: 1;
        }

        .certificate::after {
            content: '';
            position: absolute;
            top: 15mm; left: 15mm; right: 15mm; bottom: 15mm;
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

        .content {
            position: relative;
            z-index: 3;
            padding: 18mm 22mm;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Header: Logo + Institute */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12mm;
            margin-bottom: 8mm;
            padding-bottom: 6mm;
            border-bottom: 1px solid #c4a574;
        }

        .header-logo {
            height: 22mm;
            width: auto;
        }

        .institute-block {
            text-align: center;
        }

        .institute-name {
            font-size: 14pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .institute-tagline {
            font-size: 8pt;
            color: #6b5344;
            letter-spacing: 2px;
            margin-top: 1px;
        }

        .institute-website {
            font-size: 7pt;
            color: #8b7355;
            margin-top: 2px;
        }

        /* Main title */
        .cert-title {
            text-align: center;
            font-size: 28pt;
            font-weight: 700;
            color: #2c1810;
            letter-spacing: 4px;
            margin-bottom: 4mm;
            text-transform: uppercase;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 9pt;
            color: #8b7355;
            letter-spacing: 6px;
            margin-bottom: 10mm;
        }

        /* Body */
        .cert-body {
            flex: 1;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4mm 0;
        }

        .certify-line {
            font-size: 11pt;
            color: #4a3728;
            margin-bottom: 6mm;
            font-style: italic;
        }

        .recipient-name {
            font-size: 22pt;
            font-weight: 700;
            color: #2c1810;
            margin-bottom: 4mm;
            padding-bottom: 2mm;
            border-bottom: 2px solid #8b7355;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
        }

        .parent-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin-bottom: 8mm;
        }

        .course-line {
            font-size: 12pt;
            color: #4a3728;
            line-height: 1.7;
            max-width: 220mm;
            margin: 0 auto;
        }

        .course-name {
            font-weight: 700;
            color: #2c1810;
            font-size: 13pt;
        }

        .date-grade-line {
            font-size: 10pt;
            color: #5c4a3a;
            margin-top: 6mm;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 8mm;
            padding: 0 15mm;
        }

        .signature-block {
            text-align: center;
            width: 50mm;
        }

        .signature-line {
            width: 45mm;
            height: 12mm;
            border-bottom: 1px solid #4a3728;
            margin: 0 auto 3mm;
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

        /* Footer */
        .footer {
            margin-top: 6mm;
            padding-top: 4mm;
            border-top: 1px solid #c4a574;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 6mm;
        }

        .cert-meta {
            font-size: 8pt;
            color: #6b5344;
        }

        .cert-meta strong {
            color: #2c1810;
        }

        .qr-box {
            width: 16mm;
            height: 16mm;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .footer-right {
            text-align: right;
        }

        .footer-logo {
            height: 10mm;
            object-fit: contain;
        }

        .iso-text {
            font-size: 6pt;
            color: #8b7355;
            margin-top: 1px;
            letter-spacing: 0.5px;
        }

        /* Photo (optional) */
        .photo-section {
            position: absolute;
            top: 18mm;
            right: 22mm;
            z-index: 4;
        }

        .photo-box {
            width: 22mm;
            height: 28mm;
            border: 2px solid #8b7355;
            background: #faf8f5;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            background: #f0ebe3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6pt;
            color: #8b7355;
        }

        .enrollment-badge {
            margin-top: 2mm;
            font-size: 7pt;
            color: #6b5344;
            font-weight: 600;
            text-align: center;
        }

        @media print {
            body { background: white; padding: 0; }
            .certificate { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        @if($studentPhotoUrl)
        <div class="photo-section">
            <div class="photo-box">
                <img src="{{ $studentPhotoPath }}" alt="">
            </div>
            <div class="enrollment-badge">Enrol. {{ $enrollmentNumber }}</div>
        </div>
        @endif

        <div class="content">
            <header class="header">
                <img src="{{ $logoPath }}" alt="SoftPro" class="header-logo">
                <div class="institute-block">
                    <h2 class="institute-name">Softpro Skill Solutions</h2>
                    <p class="institute-tagline">Skill Development & Training Institute</p>
                    <p class="institute-website">www.softpro.co.in</p>
                </div>
            </header>

            <h1 class="cert-title">{{ $certificateTitle }}</h1>
            <p class="cert-subtitle">◆ Certificate of Achievement ◆</p>

            <div class="cert-body">
                <p class="certify-line">This is to certify that</p>
                <p class="recipient-name">{{ $salutation }} {{ $student->full_name }}</p>
                @if($parentName)
                <p class="parent-line">{{ $parentLabel }} {{ $parentName }}</p>
                @endif
                <p class="course-line">
                    has successfully completed the course <span class="course-name">{{ $course->name }}</span>
                    @if($batch)
                    (Batch: {{ $batch->batch_name }})
                    @endif
                    conducted by Softpro Skill Solutions during the period {{ $startDate }} to {{ $endDate }}
                    @if($grade && $grade !== 'N/A')
                    and has secured grade <strong>{{ $grade }}</strong>
                    @endif
                    based on overall performance, attendance and assessment.
                </p>
                <p class="date-grade-line">Issue Date: {{ $issueDate }}</p>
            </div>

            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Signatory</div>
                    <div class="signature-org">Seal</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Director</div>
                    <div class="signature-org">Softpro Skill Solutions</div>
                </div>
            </div>

            <div class="footer">
                <div class="footer-left">
                    <div class="cert-meta">
                        Certificate No. <strong>{{ $certificate->certificate_number }}</strong>
                        @if(!$studentPhotoUrl)
                        &nbsp;|&nbsp; Enrol. <strong>{{ $enrollmentNumber }}</strong>
                        @endif
                    </div>
                    @if($qrUrl)
                    <div class="qr-box">
                        <img src="{{ $qrUrl }}" alt="Verify">
                    </div>
                    @endif
                </div>
                <div class="footer-right">
                    <img src="{{ $logoPath }}" alt="SoftPro" class="footer-logo">
                    <div class="iso-text">{{ $isoText }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
