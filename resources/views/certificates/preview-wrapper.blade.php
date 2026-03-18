<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #e5e7eb;
            min-height: 100vh;
            padding: 16px;
        }
        .preview-container {
            display: inline-block;
            background: #fff;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        /* Certificate is 297mm x 210mm; scale to fit viewport */
        .preview-container .certificate {
            transform: scale(0.95);
            transform-origin: top center;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        {!! $certificateHtml !!}
    </div>
</body>
</html>
