<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERTIFICATE OF COMPLETION - {{ $certificate->certificate_number }}</title>
    <style>
        :root {
            --color-primary: #1a365d;
            --color-secondary: #2c3e6b;
            --color-accent: #2563eb;
            --color-text: #1e293b;
            --color-muted: #64748b;
            --color-border: #cbd5e1;
            --font-heading: 'Georgia', 'Times New Roman', serif;
            --font-body: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: var(--font-body);
            background: #e8eef5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .certificate-wrapper {
            position: relative;
            width: 277mm;
            height: 190mm;
            max-width: 100%;
        }

        .certificate {
            position: relative;
            width: 277mm;
            min-height: 190mm;
            max-height: 190mm;
            /* A4 landscape: 297mm x 210mm; content area 277x190 with 10mm margins */
            background: #fff;
            padding: 12mm 15mm;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 3px double var(--color-secondary);
            border-radius: 2px;
        }

        .watermark {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 120mm;
            height: auto;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
        }

        .certificate > * { position: relative; z-index: 1; }

        /* Header: Logo | Institute | Photo */
        .cert-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8mm;
            padding-bottom: 6mm;
            border-bottom: 2px solid var(--color-border);
        }

        .header-logo {
            width: 28mm;
            height: auto;
            flex-shrink: 0;
        }

        .header-center {
            flex: 1;
            text-align: center;
            padding: 0 8mm;
        }

        .institute-name {
            font-family: var(--font-heading);
            font-size: 18pt;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .institute-tagline {
            font-size: 9pt;
            color: var(--color-muted);
            letter-spacing: 1px;
        }

        .header-website {
            font-size: 8pt;
            color: var(--color-accent);
            margin-top: 2px;
        }

        .header-right {
            width: 25mm;
            flex-shrink: 0;
        }

        .photo-box {
            width: 25mm;
            height: 30mm;
            border: 2px solid var(--color-secondary);
            background: #f8fafc;
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
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6pt;
            color: var(--color-muted);
        }

        .enrollment-badge {
            margin-top: 3mm;
            font-size: 8pt;
            color: var(--color-primary);
            font-weight: 600;
        }

        .enrollment-badge span { font-weight: 700; }

        /* Title */
        .cert-title {
            font-family: var(--font-heading);
            font-size: 22pt;
            font-weight: 700;
            color: var(--color-primary);
            text-align: center;
            margin-bottom: 6mm;
            letter-spacing: 2px;
        }

        .cert-ribbon {
            display: inline-block;
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-accent) 100%);
            color: white;
            font-size: 9pt;
            font-weight: 700;
            padding: 3mm 8mm;
            margin-bottom: 6mm;
            letter-spacing: 1px;
            border-radius: 2px;
        }

        /* Body */
        .cert-body {
            font-size: 11pt;
            color: var(--color-text);
            line-height: 1.8;
            text-align: center;
            max-width: 220mm;
            margin: 0 auto 6mm;
        }

        .cert-body .highlight {
            font-weight: 700;
            color: var(--color-primary);
            font-size: 10pt;
            margin-bottom: 3mm;
        }

        .name-line {
            display: inline;
            border-bottom: 2px solid var(--color-primary);
            padding: 0 6px 2px;
            font-weight: 700;
            font-size: 12pt;
        }

        .parent-line {
            display: inline;
            border-bottom: 1px solid var(--color-text);
            padding: 0 6px 2px;
            font-weight: 600;
        }

        .course-line {
            display: inline;
            border-bottom: 2px solid var(--color-primary);
            padding: 0 6px 2px;
            font-weight: 700;
        }

        .date-line {
            display: inline;
            border-bottom: 1px solid var(--color-text);
            padding: 0 4px 2px;
        }

        .grade-line {
            display: inline;
            border-bottom: 2px solid var(--color-primary);
            padding: 0 6px 2px;
            font-weight: 700;
        }

        .parent-row { margin: 2mm 0; }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 8mm;
            padding-top: 5mm;
        }

        .signature-block {
            text-align: center;
            width: 45mm;
        }

        .signature-line {
            width: 40mm;
            border-bottom: 1px solid var(--color-text);
            margin: 0 auto 3mm;
            height: 15mm;
        }

        .signature-label {
            font-size: 9pt;
            font-weight: 600;
            color: var(--color-primary);
        }

        /* Footer */
        .cert-footer {
            margin-top: 6mm;
            padding-top: 5mm;
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-logos {
            display: flex;
            align-items: flex-end;
            gap: 8mm;
        }

        .footer-logo-text {
            font-size: 9pt;
            font-weight: 700;
            color: var(--color-primary);
        }

        .footer-logo-sub {
            font-size: 7pt;
            text-align: center;
            color: var(--color-muted);
        }

        .softpro-brand {
            text-align: right;
        }

        .footer-softpro-logo {
            height: 12mm;
            object-fit: contain;
            margin-bottom: 2px;
        }

        .iso-text {
            font-size: 7pt;
            color: var(--color-muted);
        }

        .cert-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4mm;
            font-size: 8pt;
            color: var(--color-muted);
        }

        .cert-meta-left span { font-weight: 600; color: var(--color-primary); }
        .cert-meta-right span { font-weight: 600; }

        .qr-box {
            width: 18mm;
            height: 18mm;
            flex-shrink: 0;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @media print {
            body { background: white; padding: 0; }
            .certificate-wrapper { max-width: none; box-shadow: none; }
            .certificate { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate">
            <img src="{{ $logoPath }}" alt="" class="watermark">

            <header class="cert-header">
                <div>
                    <img src="{{ $logoPath }}" alt="SoftPro" class="header-logo">
                    <div class="enrollment-badge">Enrol. <span>{{ $enrollmentNumber }}</span></div>
                </div>
                <div class="header-center">
                    <h2 class="institute-name">SOFTPRO SKILL SOLUTIONS</h2>
                    <p class="institute-tagline">Skill Development & Training Institute</p>
                    <p class="header-website">www.softpro.co.in</p>
                </div>
                @if($studentPhotoUrl)
                <div class="header-right">
                    <div class="photo-box">
                        <img src="{{ $studentPhotoPath }}" alt="Photo">
                    </div>
                </div>
                @endif
            </header>

            <h1 class="cert-title">{{ $certificateTitle }}</h1>
            <div style="text-align:center;">
                <span class="cert-ribbon">CERTIFIED</span>
            </div>

            <div class="cert-body">
                <p class="highlight">THIS IS TO CERTIFY THAT</p>
                <p>
                    {{ $salutation }} <span class="name-line">{{ $student->full_name }}</span>
                    @if($parentName)
                    <span class="parent-row"><br>{{ $parentLabel }} <span class="parent-line">{{ $parentName }}</span></span>
                    @endif
                </p>
                <p>has successfully completed the course <span class="course-line">{{ $course->name }}</span> conducted by Softpro Skill Solutions during the period <span class="date-line">{{ $startDate }}</span> to <span class="date-line">{{ $endDate }}</span>@if($grade && $grade !== 'N/A') and has secured grade <span class="grade-line">{{ $grade }}</span>@endif based on overall performance, attendance and skills.</p>
            </div>

            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Seal</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Director</div>
                    <div class="signature-label">SoftPro Skill Solutions</div>
                </div>
            </div>

            <div class="cert-footer">
                <div class="footer-logos">
                    <div>
                        <div class="footer-logo-text">JAS-ANZ</div>
                    </div>
                    <div>
                        <div class="footer-logo-text">LMS</div>
                        <div class="footer-logo-sub">LINEAR MANAGEMENT<br>SOLUTIONS</div>
                    </div>
                </div>
                <div class="softpro-brand">
                    <img src="{{ $logoPath }}" alt="SoftPro" class="footer-softpro-logo">
                    <div class="iso-text">{{ $isoText }}</div>
                </div>
            </div>

            <div class="cert-meta">
                <div class="cert-meta-left">
                    Certificate No. <span>{{ $certificate->certificate_number }}</span> &nbsp;|&nbsp; Issue Date: <span>{{ $issueDate }}</span>
                </div>
                <div class="cert-meta-right">
                    @if($qrUrl)
                    <div class="qr-box">
                        <img src="{{ $qrUrl }}" alt="QR Code">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        window.__CERT_DATA__ = {
            enrollment_no: "{{ $enrollmentNumber }}",
            certificate_no: "{{ $certificate->certificate_number }}",
            issue_date: "{{ $issueDate }}",
            student_name: "{{ $student->full_name }}",
            parent_name: "{{ $parentName }}",
            course_name: "{{ $course->name }}",
            batch_name: "{{ $batch?->batch_name ?? '' }}",
            from_date: "{{ $startDate }}",
            to_date: "{{ $endDate }}",
            grade: "{{ $grade }}",
            student_photo_url: "{{ $studentPhotoUrl ?? '' }}",
            qr_url: "{{ $qrUrl ?? '' }}"
        };
    </script>
</body>
</html>
